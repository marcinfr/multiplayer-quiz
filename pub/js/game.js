var update = function() {
    $.ajax({
       type : 'GET',
       url : 'gameJson',
       success : function(data){
            $(".section").hide();
            let parsedData = JSON.parse(data);
            for (const [key, elems] of Object.entries(parsedData)) {
                $('#section-' + key).show();
                console.log(elems);
                for (const [id, content] of Object.entries(elems)) {
                    $('#' + id).html(content);
                }
            }
       },
   });
};
update();
var refInterval = window.setInterval('update()', 300); 