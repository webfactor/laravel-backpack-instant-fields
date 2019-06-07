<div @include('crud::inc.field_wrapper_attributes') >
    @if ($field['on_the_fly']['create'] ?? true)
        @include('webfactor::fields.inc.button-add')
    @endif
    <table id="related_{{ $field['name'] }}"
           class="table table-striped table-hover display responsive nowrap dataTable dtr-inline"
           width="100%">
        <thead>
        <tr>
            @foreach($field['data']['columns'] as $label)
                {!! "<th>".$label."</th>" !!}
            @endforeach
        </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <tr>
            @foreach($field['data']['columns'] as $label)
                {!! "<th>".$label."</th>" !!}
            @endforeach
        </tr>
        </tfoot>
    </table>

</div>

@push('after_styles')
    <!-- DATA TABLES -->
    <link href="//cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css">
@endpush

@push('crud_fields_scripts')
    <script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap.min.js" type="text/javascript"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $.fn.dataTable.moment('DD.MM.YYYY');

            var table = $('#related_{{ $field['name'] }}').DataTable({
                ajax: {
                    url: "{{ $field['data']['source'] ?? backpack_url('task/ajax/table') }}?entity_id={{ $entry->id }}&field={{ $field['name'] }}",
                    dataSrc: "data"
                },
                columns: [
                        @foreach($field['data']['columns'] as $key => $label)
                    {
                        data: "{{ $key }}"
                    },
                    @endforeach
                ],
                order: {!! $field['data']['order'] ?? '[[0, "asc"]]' !!}
            });

            // load create modal content
            $("#{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_create_modal").on('show.bs.modal', function (e) {
                var loadurl = $(e.relatedTarget).data('load-url');
                var form = $(e.relatedTarget).closest('form');

                var data = form.serializeArray().filter(function (index) {
                    return $.inArray(index.name, <?php echo json_encode($field['on_the_fly']['serialize'] ?? []); ?>) >= 0;
                });

                $(this).find('.modal-content').load(loadurl + '&' + $.param(data));
            });

            $("#{{ $field['on_the_fly']['entity'] ?? 'ajax_entity' }}_create_modal").on("hidden.bs.modal", function () {
                table.draw();
            });
        });
    </script>
@endpush
