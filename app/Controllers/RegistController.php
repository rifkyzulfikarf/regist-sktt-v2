<?php

namespace App\Controllers;

use App\Libraries\BarcodeCrypto;
use App\Models\ParticipantModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorPNG;

class RegistController extends BaseController
{
    public function index()
    {
        $participantModel = new ParticipantModel();

        return view('participant/home', [
            'positions' => $participantModel->getDistinctPositions(),
        ]);
    }

    public function generateCard()
    {
        $rules = [
            'participant_number' => 'required|max_length[100]',
            'position'           => 'required|max_length[255]',
            'birth_date'         => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validateData($this->request->getPost(array_keys($rules)), $rules)) {
            return redirect()->back()->withInput()->with('error', validation_list_errors());
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

        $crypto = new BarcodeCrypto();
        $encryptedPayload = $crypto->encryptParticipantNumber($participant['participant_number']);

        $generator = new BarcodeGeneratorPNG();
        $barcodePng = $generator->getBarcode($encryptedPayload, $generator::TYPE_CODE_128, 2, 70);

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
            'encryptedPayload'  => $encryptedPayload,
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
            ->setHeader('Content-Disposition', 'inline; filename="kartu_ujian_' . $safeNumber . '.pdf"')
            ->setBody($dompdf->output());
    }
}
