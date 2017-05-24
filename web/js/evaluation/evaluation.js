$('document').ready(function(){
    function initCheckedBox(){
        $("input:radio").each(function(e) {
            $(this).prop('checked',false);
        });
    }

    function updateQuestionAndResponseBloc(data) {
        var nextQuestion = JSON.parse(data.nextQuestion);
        $('#question').html(nextQuestion.enonce);
        $('#question').attr('data-id',nextQuestion.id);
        $('#questionsContainer').find('.jqChoices').remove()
        var choicesContainer = $('#jqChoicesContainer'),
            responseList = JSON.parse(data.responseList);

        $.each(responseList, function(key, val){ 
            var row = $('<tr>'),
                column = $('<td class="jqChoices">'),  
                input = jQuery('<input/>', {
                class:'dynamicInput jqChoice',
                type: 'checkbox',
                value: key,
            }),
            label = jQuery('<span/>', {'text':val});
            column.append(input).append(label);
            row.append(column);
            row.insertAfter($('#questionsContainer').children('tr:first'));
        })
    }
    

    function getParameters(direction, buttonId){
        var parameters = {};
        parameters['sessionId'] = $('#sId').val();
        parameters['buttonId'] = buttonId;
        parameters['qNumber']  = questionNumber;
        parameters['direction']= direction;
        parameters['responseItemIds'] = getResponseItemId();
        parameters['questionId'] = parseInt($("#question").attr('data-id'));

        return parameters;
    }  
    
    function getResponseItemId(itemList)
    {
        var selected = [];
        $("input:checkbox:checked").each(function() {
            selected.push($(this).val());
        });
        
        return selected;
    }

    var path = $('#path').val();

    function sendAjaxRequestForloadNewQuestions(parameters, sessionQuestion, path, isEndTime){
        $.ajax({
            type:"POST",
            url: path,
            dataType :'json',
            data : { 'parameters': parameters,'sessionQuestion': sessionQuestion, 'isEndTime':isEndTime, },
            cache: false,
            success: function(data)
            {   
                updateQuestionAndResponseBloc(data);
            }
        });
    }

    $('#jqValidate').on('click',function(e) {
        var parameters =  getParameters(1, $(this).attr('id'));
        questionNumber++;
      
        sendAjaxRequestForloadNewQuestions(parameters,sessionQuestion[questionNumber], url);
   });
});