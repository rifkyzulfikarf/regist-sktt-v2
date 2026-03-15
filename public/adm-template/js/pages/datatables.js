$(document).ready(function () {

    "use strict";

    $('#datatable1').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: 'Excel',
            customizeData: function(data) {
                for(var i = 0; i < data.body.length; i++) {
                    for(var j = 0; j < data.body[i].length; j++) {
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            }        
        }],
        sort: false,
        rowCallback: function(row, data) {}
    });

    $('#datatable2').DataTable({
        "scrollY": "300px",
        "scrollCollapse": true,
        "paging": false
    });

    $('#datatable3').DataTable({
        "scrollX": true
    });

    $('#datatable4 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#datatable4').DataTable({
        initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
                var that = this;
 
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
        }
    });
});