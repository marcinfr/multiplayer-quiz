var update = function() {
    $.ajax({
       type : 'GET',
       url : 'gameJson',
       success : function(data){
            let parsedData = JSON.parse(data);
            if (gameHash == parsedData['hash']) {
                return;
            }
            $(".section").hide();
            gameHash = parsedData['hash'];
            for (const [key, elems] of Object.entries(parsedData)) {
                $('#section-' + key).show();
                for (const [id, content] of Object.entries(elems)) {
                    $('#' + id).html(content);
                }
            }
            $('.answer').on('click', function() {
                const answer_id = $(this).data('index');
                $.ajax({
                    type : 'GET',
                    url : 'selectAnswer?id=' + answer_id,
                });
            });
       },
   });
};
update();
var gameHash = "";
var refInterval = window.setInterval('update()', 300); 