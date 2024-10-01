let togling = 0;
// if (togling % 2 === 0){
//     // $('#sidebar-wrapper').attr('style', 'display: none;');
//     $('#wrapper').toggleClass('toggled');
// }

$(document).ready(function() {
    $('#menu-toggle').click(function() {
        // $('#sidebar-wrapper').attr('style', 'display: block;');
        if (togling=== 0){
            setTimeout(()=>{
                $('#wrapper').toggleClass('toggled');
            }, 0);
        } else if (togling < 0) {
            $('#wrapper').toggleClass('toggled');
        }
        togling--;
        // alert('hello');
    });
});


