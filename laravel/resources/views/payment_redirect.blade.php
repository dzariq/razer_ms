<form method="post" id="form_payment_razer" action="{{ $data->RequestURL }}">
    @csrf <!-- Include CSRF token for security -->

    @foreach($data->RequestData as $key=>$item)
    <input type="text" id="name" name="{{$key}}" value="{{ $item }}">
    @endforeach

</form>

<script>
        // Get the form element by its ID
        var form = document.getElementById('form_payment_razer');

        // Submit the form
        form.submit();
    </script>