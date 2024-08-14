<script>
    function deleteModel(button) {
        let model_id = $(button).data('id');
        let url = "{{ route($route, ':id') }}";
        url = url.replace(':id', model_id);
        $("#deleteForm").attr('action', url);
    }
</script>