jQuery(document).ready(function () {
    jQuery('#example').DataTable(
        {
            columnDefs: [
                { orderable: false, targets: 0 }
            ],
            order: [[8, 'asc']]
        }
    );
});