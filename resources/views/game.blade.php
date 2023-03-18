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
    <div class="reset-game">
      <div class="text-center">
        <a href="/reset" class="btn btn-danger">RESET GAME</a>


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

  $(document).on('click', '.chat-suggest li', function() {
    $('.message').val($(this).text());
    $('.btn-send').click();
  });
  $('.btn-send').click(function(){
    $('.loader').fadeIn(function() {
      initGame();
    });
  });

  function initGame() {
    let url = "/chat";
    let message = "Start game";
    let image_description = "";
    if($('.message').val()) {
      message = $('.message').val();
      $(".chat-box").append("<div class='chat-box-message user'>" + message + "</div>");
    }

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
          if(data.messages == null) {

          }
          // var string = JSON.stringify(data.messages);

          const jsonString = data.messages;
          const jsonStartIndex = jsonString.indexOf('{');
          const jsonEndIndex = jsonString.lastIndexOf('}');
          const jsonSubstring = jsonString.substring(jsonStartIndex, jsonEndIndex + 1);
          const jsonContentLast = jsonString.substring(jsonEndIndex + 1)
          const jsonContent = JSON.parse(jsonSubstring);

          // var content = JSON.parse(string);

          $(".chat-box").append("<div class='chat-box-message assistant'>" + jsonContent.description + "<br>" + jsonContentLast + "</div>");



          $(".status-location").text(jsonContent.location);
          $(".status-weather").text(jsonContent.weather);
          $(".status-health").text(jsonContent.health);
          $(".status-gold").text(jsonContent.gold);
          $(".chat-suggest").empty();
          $.each(jsonContent.possible_commands, function(k, v) {
            $(".chat-suggest").append("<li>" + v + "</li>");
          });

          $(".status-inventory").empty();
          $.each(jsonContent.inventory, function(k, v) {
            $(".status-inventory").append("<li>" + v + "</li>");
          });
          image_description = jsonContent.image_description;
          $('.loader').fadeOut();
          $(".chat-box").scrollTop($(".chat-box")[0].scrollHeight);

              generateImage(image_description);
        }
    });

  }


  </script>
</body>

</html>
