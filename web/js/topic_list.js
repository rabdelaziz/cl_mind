$('document').ready(function(){
 ///   $(".modall").modal('show');

    $('body').on('click', '#createTopic', function(){
     getForm();
    });



    function getForm(path)
    {
        $.ajax({
            type:"POST",
            url: path,
            data : data,
            cache: false,
            success: function(response)
            {

            }
        });
    }
});
