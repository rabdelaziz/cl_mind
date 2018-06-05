$('document').ready(function(){
    $(".jqTimer").TimeCircles().start();
    function initCheckedBox(){
        $("input:radio").each(function(e) {
            $(this).prop('checked',false);
        });
    }
    
    function initProgressBar(validQuestionNumber, totalQuestionNumber)
    {
        var progressVal =  Math.round((parseInt(validQuestionNumber) / totalQuestionNumber) * 100);
        $('#progressBarVal').attr("aria-valuenow",progressVal);
        $('#progressBarVal').attr("style","width:"+progressVal+"%");
        $('#progressLabel').html('Avancement: '+ Math.round((parseInt(validQuestionNumber) / totalQuestionNumber) * 100) + '%')
    }

    function updateQuestionAndResponseBloc(data) {
        //alert('1')
      //  console.log(parseInt(data.questionNumber))
       // var question
        if(parseInt(data.questionNumber) == 0){
            var url = Routing.generate('evaluation_result')
            location.href = url;
        }

        var nextQuestion = JSON.parse(data.nextQuestion);
        $('#question').html(nextQuestion.content);
        $('#question').attr('data-id',nextQuestion.id);
        $('#questionsContainer').find('tr.jqChoices').remove()
        var choicesContainer = $('#jqChoicesContainer'),
            responseList = JSON.parse(data.responseList);

        $.each(responseList, function(key, val){ 
            var row = $('<tr class="jqChoices">'),
                column = $('<td >'),  
                input = jQuery('<input/>', {
                class:'dynamicInput jqChoice',
                type: 'checkbox',
                value: key,
            }),
            label = jQuery('<span/>', {'text':val});
            column.append(input).append(label);
            row.append(column);
            row.insertAfter($('#questionsContainer').children('tr:last'));
           // $('#questionsContainer').find('tr:last').append(row);

        });
        initProgressBar(data.validQuestionNumber,data.totalQuestionNumber)
         /*var progressVal = (parseInt(data.validQuestionNumber)/parseInt(data.totalQuestionNumber)) * 100
            $('#progressBarVal').attr("aria-valuenow",progressVal); 
            $('#progressBarVal').attr("style","width:"+progressVal+"%"); 
            $('#progressLabel').html('Avancement: '+ ((parseInt(data.validQuestionNumber) / data.totalQuestionNumber) * 100) + '%')
*/
            $('#jqQuesValid').html(parseInt(data.validQuestionNumber));
            $('#jqQuesNotvalid').html(data.questionNumber);
            // console.log(nextQuestion.duration)
            console.log( parseInt(nextQuestion.duration))
        console.log( parseInt(nextQuestion.duration))
            $('.jqTimer').data('timer', parseInt(nextQuestion.duration));
            $(".jqTimer").TimeCircles().restart();

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

    function sendAjaxRequestForloadNewQuestions(parameters, sessionQuestion, path, isEndTime) {

        $.ajax({
            type: "POST",
            url: path,
            dataType: 'json',
            data: {'parameters': parameters, 'sessionQuestion': sessionQuestion, 'isEndTime': isEndTime,},
            cache: false,
            success: function (data) {
              //  console.log(data)
                if (data.questionNumber == 1) {
                    $('#jqValidate').html('Terminer l\'évaluation');

                }
                updateQuestionAndResponseBloc(data);

            }
        });
    }
    $('#jqValidate').on('click',function(e) {

        var parameters =  getParameters(1, $(this).attr('id'));
        questionNumber++;
      
        sendAjaxRequestForloadNewQuestions(parameters,sessionQuestion[questionNumber], url);
   });

    initProgressBar(validQuestionNumber, totalQuestionNumber);
});