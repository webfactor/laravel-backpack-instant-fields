<div @include('crud::inc.field_wrapper_attributes') >

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

            $('#related_{{ $field['name'] }}').DataTable({
                ajax: {
                    url: "{{ backpack_url('task/ajax/table') }}?entity_id={{ $entry->id }}&field={{ $field['name'] }}",
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
        });
    </script>
@endpush
