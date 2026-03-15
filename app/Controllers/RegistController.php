<?php

namespace App\Controllers;

use App\Models\ParticipantModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorPNG;

class RegistController extends BaseController
{
    public function index()
    {
        $participantModel = new ParticipantModel();
        $captcha = $this->generateCaptcha();

        return view('participant/home', [
            'positions' => $participantModel->getDistinctPositions(),
            'captchaImage' => $captcha['image'],
        ]);
    }

    public function generateCard()
    {
        $rules = [
            'participant_number' => 'required|max_length[100]',
            'position'           => 'required|max_length[255]',
            'birth_date'         => 'required|valid_date[Y-m-d]',
            'captcha'            => 'required|max_length[10]',
        ];

        if (! $this->validateData($this->request->getPost(array_keys($rules)), $rules)) {
            return redirect()->back()->withInput()->with('error', validation_list_errors());
        }

        $captchaInput = strtoupper(trim((string) $this->request->getPost('captcha')));
        $captchaWord = strtoupper((string) session()->get('captcha_word'));
        if ($captchaWord === '' || $captchaInput !== $captchaWord) {
            return redirect()->back()->withInput()->with('error', 'Captcha tidak sesuai.');
        }

        $participantModel = new ParticipantModel();
        $participant = $participantModel->findVerified(
            (string) $this->request->getPost('participant_number'),
            (string) $this->request->getPost('position'),
            (string) $this->request->getPost('birth_date')
        );

        if (! $participant) {
            return redirect()->back()->withInput()->with('error', 'Data peserta tidak ditemukan. Pastikan Nomor Peserta, Jabatan, dan Tanggal Lahir sesuai.');
        }

        $barcodePayload = md5((string) $participant['participant_number']);

        $generator = new BarcodeGeneratorPNG();
        $barcodePng = $generator->getBarcode($barcodePayload, $generator::TYPE_CODE_128, 2, 70);

        $logoBase64 = null;
        $logoPath = FCPATH . 'images/kemenham_icon.png';
        if (is_file($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath));
        }

        $rawData = [];
        if (! empty($participant['raw_data'])) {
            $decoded = json_decode($participant['raw_data'], true);
            if (is_array($decoded)) {
                $rawData = $decoded;
            }
        }

        $html = view('participant/pdf_card', [
            'participant'       => $participant,
            'participantData'   => $rawData,
            'barcodeBase64'     => 'data:image/png;base64,' . base64_encode($barcodePng),
            'barcodePayload'    => $barcodePayload,
            'logoBase64'        => $logoBase64,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $participant['participant_number']);

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="kartu_ujian_' . $safeNumber . '.pdf"')
            ->setBody($dompdf->output());
    }

    private function generateCaptcha(): array
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $word = '';

        for ($i = 0; $i < 5; $i++) {
            $word .= $characters[random_int(0, strlen($characters) - 1)];
        }

        session()->set('captcha_word', $word);

        $x = 14;
        $glyphs = '';
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            $y = random_int(28, 38);
            $rot = random_int(-12, 12);
            $glyphs .= '<text x="' . $x . '" y="' . $y . '" transform="rotate(' . $rot . ' ' . $x . ' ' . $y . ')" fill="#0e2a5a" font-size="24" font-family="Arial, sans-serif" font-weight="700">' . $char . '</text>';
            $x += 30;
        }

        $lines = '';
        for ($i = 0; $i < 6; $i++) {
            $x1 = random_int(0, 180);
            $y1 = random_int(0, 52);
            $x2 = random_int(0, 180);
            $y2 = random_int(0, 52);
            $lines .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="#3f7dc3" stroke-width="1" opacity="0.45" />';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="180" height="52" viewBox="0 0 180 52">'
            . '<rect width="180" height="52" fill="#edf3fb" rx="6" />'
            . $lines
            . $glyphs
            . '</svg>';

        return [
            'word' => $word,
            'image' => 'data:image/svg+xml;base64,' . base64_encode($svg),
        ];
    }
}
