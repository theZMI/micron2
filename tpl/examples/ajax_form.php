<head>
    <script>
        // prepare the form when the DOM is ready
        $(function () {
            const options = {
                beforeSubmit: showRequest,  // pre-submit callback
                success: showResponse,  // post-submit callback

                // other available options:
                //url:       url         // override for form's 'action' attribute
                //type:      type        // 'get' or 'post', override for form's 'method' attribute
                //dataType: null         // 'xml', 'script', or 'json' (expected server response type)
                //clearForm: true        // clear all form fields after successful submit
                //resetForm: true        // reset the form after successful submit

                // $.ajax options can be used here too, for example:
                //timeout:   3000
            };

            // AJAX-SUBMIT VERSION:
            // bind to the form's submit event
            // $('#myForm1').on('submit', function() {
            //     // inside event callbacks 'this' is the DOM element so we first
            //     // wrap it in a jQuery object and then invoke ajaxSubmit
            //     $(this).ajaxSubmit(options);
            //
            //     // !!! Important !!!
            //     // always return false to prevent standard browser submit and page navigation
            //     return false;
            // });

            // AJAX-FORM VERSION:
            $('#myForm1').ajaxForm(options);
        });

        // pre-submit callback
        function showRequest(formData, jqForm, options) {
            // formData is an array; here we use $.param to convert it to a string to display it
            // but the form plugin does this for you automatically when it submits the data
            const queryString = $.param(formData);

            // jqForm is a jQuery object encapsulating the form element.  To access the
            // DOM element for the form do this:
            // var formElement = jqForm[0];

            console.log('About to submit: \n\n' + queryString);

            // here we could return false to prevent the form from being submitted;
            // returning anything other than false will allow the form submit to continue
            return true;
        }

        // post-submit callback
        function showResponse(responseText, statusText, xhr, $form) {
            // for normal html responses, the first argument to the success callback
            // is the XMLHttpRequest object's responseText property

            // if the ajaxSubmit method was passed an Options Object with the dataType
            // property set to 'xml' then the first argument to the success callback
            // is the XMLHttpRequest object's responseXML property

            // if the ajaxSubmit method was passed an Options Object with the dataType
            // property set to 'json' then the first argument to the success callback
            // is the json data object returned by the server

            $('#my-alert').html(
                'status: ' + statusText +
                '<br>responseText: ' + responseText +
                '<br>The output div should have already been updated with the responseText.'
            )
        }

        // prepare the form when the DOM is ready
        $(function () {
            const options = {
                beforeSubmit: showRequest,  // pre-submit callback
                success: showResponse,  // post-submit callback

                // other available options:
                //url:       url         // override for form's 'action' attribute
                //type:      type        // 'get' or 'post', override for form's 'method' attribute
                //dataType: null         // 'xml', 'script', or 'json' (expected server response type)
                //clearForm: true        // clear all form fields after successful submit
                //resetForm: true        // reset the form after successful submit

                // $.ajax options can be used here too, for example:
                //timeout:   3000
            };

            // AJAX-SUBMIT VERSION:
            // bind to the form's submit event
            // $('#myForm1').on('submit', function() {
            //     // inside event callbacks 'this' is the DOM element so we first
            //     // wrap it in a jQuery object and then invoke ajaxSubmit
            //     $(this).ajaxSubmit(options);
            //
            //     // !!! Important !!!
            //     // always return false to prevent standard browser submit and page navigation
            //     return false;
            // });

            // AJAX-FORM VERSION:
            $('#myForm1').ajaxForm(options);

            $('#myForm2').ajaxForm({
                success: (jsonResponse) => {
                    if (!jsonResponse.is_success) {
                        return;
                    }
                    $('#my-alert-2').html(
                        jsonResponse.response +
                        '<br>All object: ' + JSON.stringify(jsonResponse)
                    );
                }
            });
        });
    </script>
</head>

<h1>Html response:</h1>
<div class="alert alert-primary" role="alert" id="my-alert"></div>
<form action="<?= GetCurUrl('get_response=1') ?>" method="post" id="myForm1">
    <input type="hidden" name="param_1" value="Hello">
    <input type="hidden" name="param_2" value="World">
    <input type="hidden" name="param_3" value="<?= date('d-m-Y H:i:s') ?>">
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

<br>

<h1>JSON response:</h1>
<div class="alert alert-primary" role="alert" id="my-alert-2"></div>
<form action="<?= GetCurUrl('get_response=1&type=json') ?>" method="post" id="myForm2">
    <input type="hidden" name="param_1" value="Hello">
    <input type="hidden" name="param_2" value="World">
    <input type="hidden" name="param_3" value="<?= date('d-m-Y H:i:s') ?>">
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>