<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Game</title>

    <!-- Bootstrap -->
    <link href="{{asset('game/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('game/css/rest.css?v1')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <div class="loader"></div>
  <div class="container">
    <div class="content-holder">
      <div class="row">
        <div class="col-md-4">
          <div class="image-box">

          </div>
          <div class="status-box">
            <div class="col-md-6">
              <h4>Location</h4>
              <span class="status-location"></span>
            </div>
            <div class="col-md-6">
              <h4>Weather</h4>
              <span class="status-weather"></span>
            </div>
            <div class="col-md-6">
              <h4>Health</h4>
              <span class="status-health">0</span>
            </div>
            <div class="col-md-6">
              <h4>Gold</h4>
              <span class="status-gold">0</span>
            </div>
            <hr>
            <div class="col-md-12">
              <h4>Inventory</h4>

              <ul class="status-inventory">
              </ul>
            </div>


          </div>
          <a href="/reset">RESET</a>
        </div>
        <div class="col-md-8">
          <div class="chat-box">

          </div>
          <div class="chat-control">
            <ul class="chat-suggest">

            </ul>
            <div class="row">
              <div class="col-md-10">
                <input type="text" class="form-control message">
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-send">SEND</button>
              </div>

            </div>

          </div>


        </div>

      </div>

    </div>

  </div>
  <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script src="{{asset('game/js/bootstrap.min.js')}}"></script>
  <script>
  function generateImage(description) {

    let imageDescription = description;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        url: '../get-image',
        datatype: 'JSON',
        type: 'POST',
        global: false,
        async: false,
        data: {
            "description": imageDescription
        },
        success: function (data, status) {
          $('.image-box').empty();
          $('.image-box').append("<img src='"+data.image+"'>");
          console.log();
        }

      });

  };
  $('.message').keyup(function(event) {
    if (event.keyCode === 13) {
        $('.btn-send').click();
    }
  });
  $('.chat-suggest li').click(function(){
    $('.message').val($(this).text());
    $('.btn-send').click();
  });
  $('.btn-send').click(function(){
    $('.loader').fadeIn(function() {
      let url = "/chat";
      let message = $('.message').val();
      $(".chat-box").append("<div class='chat-box-message user'>" + message + "</div>");
      $('.message').val('');
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          url: url,
          datatype: 'JSON',
          type: 'POST',
          global: false,
          async: false,
          data: {
              "message": message
          },
          success: function (data, status) {
            var content = JSON.parse(data.messages.content);
            console.log(content.description);
            if(content.description) {
              $(".chat-box").append("<div class='chat-box-message assistant'>" + content.description + "</div>");
            } else {
              $(".chat-box").append("<div class='chat-box-message assistant'>" + data.messages.content + "</div>");
            }

            $(".status-location").text(content.location);
            $(".status-weather").text(content.weather);
            $(".status-health").text(content.health);
            $(".status-gold").text(content.gold);
            $(".chat-suggest").empty();
            $.each(content.possible_commands, function(k, v) {
              $(".chat-suggest").append("<li>" + v + "</li>").on('click', 'ul li', function () {
                $('.message').val($(this).text());
                $('.btn-send').click();
              });
            });

            $(".status-inventory").empty();
            $.each(content.inventory, function(k, v) {
              $(".status-inventory").append("<li>" + v + "</li>");
            });
            generateImage(content.image_description);


            // var content = JSON.parse(data.messages);
            // if(content) {
            //   $(".chat-box").append("<div class='chat-box-message assistant'>" + content.description + "</div>");
            //
            // } else {
            //
            //
            // }

            // $(".chat-box").empty();
            // $.each(data.messages, function(k, v) {
            //
            //
            //   if(v.role === 'assistant') {
            //     var content = JSON.parse(v.content)
            //     // console.log(content);
            //     if(content) {
            //       $(".chat-box").append("<div class='chat-box-message assistant'>" + content.description + "</div>");
            //
            //     } else {
            //       $(".chat-box").append("<div class='chat-box-message assistant'>" + v.content + "</div>");
            //
            //     }
            //
            //   } else {
            //     $(".chat-box").append("<div class='chat-box-message user'>" + v.content + "</div>");
            //
            //   }
            //
            // });
            $('.loader').fadeOut();
            $(".chat-box").scrollTop($(".chat-box")[0].scrollHeight);

          }
      });


    })

  });


  </script>
</body>

</html>
