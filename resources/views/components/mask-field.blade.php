@php
    $mask = $mask ?? '+999 99 999-99-99'
@endphp
<script>
    Inputmask('{{ $mask }}').mask(document.getElementById('{{ $fieldId }}'))
</script>
