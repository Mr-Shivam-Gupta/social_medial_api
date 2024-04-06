$(document).ready(function(){
  $('.users').click(function(){
    $('.start_chat_text').hide();
  })
  // console.log(Echo);
  Echo.channel('status')
    .listen('StatusEvent', (event) => {
        console.log(event);
    })
});
