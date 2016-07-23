$('document').ready(function(){
 ///   $(".modall").modal('show');
    var dialog, form;
    dialog =  $("#dialog-form").dialog(event, {
        autoOpen: false,
        width: '30%',
        modal: true,
        closeText: "X",
        title: "Ajouter un nouveau theme",
        buttons: [],
        close: function( event, ui ) {dialog.dialog('close');},
    });



    $( "#createTopic" ).button().on( "click", function() {
        dialog.dialog( "open" );
    });

    $('body').on('click', '#createTopic', function(){
         var formContainer = $('.modal-body');
         getForm('create_topic', []);
    }).on('submit', '#createTopicForm', function(e){
        e.preventDefault();
        save('create_topic', $(this).serialize());
    });

    function getForm(routeName, data)
    {
        var url = Routing.generate(routeName)
        $.ajax({
            type:"GET",
            url: url,
            data : data,
            cache: false,
            success: function(response)
            {
                dialog.html(response);
            }
        });
    }
    function save(routeName, data)
    {
        var path = Routing.generate(routeName);
        $.ajax({
            url: path,
            data:   data,
            method: 'POST',
            success: function (response) {
               // dialogLoader.hide();
                if(response.status == 'ok') {
                    $("#dialog-form").empty();
                        addSuccessMsgToDialogBox(response.msg);

                        setTimeout(function(){ dialog.dialog("close")}, 2000);
                } else {
                    $('#errorMsg').append(response.msg)
                    $('#formError').show();
                }
            },
            error: function() {}
        });
    }

    function addSuccessMsgToDialogBox(msg){
        jQuery('<div/>', {
            'class': 'success',
            text: msg
        }).prependTo('#dialog-form');
    }
});
