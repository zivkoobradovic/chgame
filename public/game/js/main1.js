var game = 1;
///GAME TRACK///


var trackHolder = $('.track-list'),
    trackList = trackHolder.find('li').detach(),
    usedTrackList = [],
    newTrackList = [];
    while (true) {
      var liTrack = trackList[Math.floor(Math.random() * trackList.length)];
      if (usedTrackList.indexOf(liTrack) === -1 && newTrackList.indexOf(liTrack) === -1) newTrackList.push(liTrack);
      if (newTrackList.length >= 6) break;
    }
    usedTrackList = newTrackList;
    trackHolder.html(newTrackList);

$('.track-box').draggable( {
  containment: '#game-1',
  stack: '.track-box',
  cursor: 'move',
  revert: true
} );
$('.track-slot').droppable( {
  accept: '.track-box',
  hoverClass: 'hovered',
  drop: handleTrackDrop
} );
function handleTrackDrop(event, ui) {
  var id = ui.draggable.data('track');
  if(id === 1) {
  $('.track-box').draggable('disable');
  ui.draggable.position({
    of: $(this), my: 'center center', at: 'center center'
  });
  ui.draggable.addClass('correct');
  ui.draggable.draggable('option', 'revert', false);
  nextGame();
} else {
  wrongPlay();
  ui.draggable.addClass('wrong');
}
}
///GAME PART///
var partHolder = $('.part-list'),
    partList = partHolder.find('li').detach(),
    usedPartList = [],
    newPartList = [];
    while (true) {
      var liPart = partList[Math.floor(Math.random() * partList.length)];
      if (usedPartList.indexOf(liPart) === -1 && newPartList.indexOf(liPart) === -1) newPartList.push(liPart);
      if (newPartList.length >= 3) break;
    }
    usedPartList = newPartList;
    partHolder.html(newPartList);

$('.part-box').draggable( {
  containment: '#game-2',
  stack: '.part-box',
  cursor: 'move',
  revert: true
} );

$('.part-slot').droppable( {
  accept: '.part-box',
  hoverClass: 'hovered',
  drop: handlePartDrop
} );

var correctPart = 0;
function handlePartDrop(event, ui) {
  var slotPart = $(this).data('part');
  var boxPart = ui.draggable.data('part');
  if (slotPart === boxPart) {
    ui.draggable.draggable('disable');
    ui.draggable.position({
      of: $(this), my: 'center center', at: 'center center'
    });
    ui.draggable.addClass('correct-part');
    ui.draggable.draggable('option', 'revert', false);
    correctPlay();
    correctPart++;
    if(correctPart === 3) {
      nextGame();
    }
  } else {
    wrongPlay();
  }
}
///GAME FLAG///
var flagHolder = $('.flag-list'),
    flagList = flagHolder.find('li').detach(),
    usedFlagList = [],
    newFlagList = [];
    while (true) {
      var liFlag = flagList[Math.floor(Math.random() * flagList.length)];
      if (usedFlagList.indexOf(liFlag) === -1 && newFlagList.indexOf(liFlag) === -1) newFlagList.push(liFlag);
      if (newFlagList.length >= 6) break;
    }
    usedFlagList = newFlagList;
    flagHolder.html(newFlagList);
$('.flag-box').draggable( {
  containment: '#game-3',
  stack: '.flag-box',
  cursor: 'move',
  revert: true
} );

$('.flag-slot').droppable( {
  accept: '.flag-box',
  hoverClass: 'hovered',
  drop: handleFlagDrop
} );
function handleFlagDrop(event, ui) {
  var id = ui.draggable.data('box');
  if(id === 122) {
  ui.draggable.draggable('disable');
  $('.flag-box').draggable('disable');
  ui.draggable.position({
    of: $(this), my: 'top left', at: 'top left'
  });
  ui.draggable.addClass('correct');
  ui.draggable.draggable('option', 'revert', false);
  nextGame();
  } else {
    wrongPlay();
    ui.draggable.addClass('wrong');
  }
}
///GAME WINNER///
var winnerHolder = $('.winner-list'),
    winnerList = winnerHolder.find('li').detach(),
    usedWinnerList = [],
    newWinnerList = [];
    while (true) {
      var liWinner = winnerList[Math.floor(Math.random() * winnerList.length)];
      if (usedWinnerList.indexOf(liWinner) === -1 && newWinnerList.indexOf(liWinner) === -1) newWinnerList.push(liWinner);
      if (newWinnerList.length >= 2) break;
    }
    usedWinnerList = newWinnerList;
    winnerHolder.html(newWinnerList);
$('.winner-box').draggable( {
  containment: '#game-5',
  stack: '.winner-box',
  cursor: 'move',
  revert: true
} );

$('.winner-slot').droppable( {
  accept: '.winner-box',
  hoverClass: 'hovered',
  drop: handleWinnerDrop
} );

var correctWinner = 0;
function handleWinnerDrop(event, ui) {
  var slotWinner = $(this).data('winner');
  var boxWinner = ui.draggable.data('winner');
  if (slotWinner === boxWinner) {
    ui.draggable.draggable('disable');
    ui.draggable.position({
      of: $(this), my: 'top left', at: 'top left'
    });
    ui.draggable.addClass('correct-winner');
    ui.draggable.draggable('option', 'revert', false);
    correctPlay();
    correctWinner++;
    if(correctWinner === 2) {
      nextGame();
    }
  } else {
      wrongPlay();
  }
}
///GAME DRIVER///
$('.suit-box').draggable( {
  containment: '#game-4',
  stack: '.suit-box',
  cursor: 'move',
  revert: true
} );
$('.suit-slot').droppable( {
  accept: '.suit-box',
  hoverClass: 'hovered',
  drop: handleSuitDrop
} );
var correctSuit = 0;
function handleSuitDrop(event, ui) {
  var slotSuit = $(this).data('suit');
  var boxSuit = ui.draggable.data('suit');
  if (slotSuit === boxSuit) {
    ui.draggable.draggable('disable');
    ui.draggable.position({
      of: $(this), my: 'center center', at: 'center center'
    });
    ui.draggable.addClass('correct-suit');
    ui.draggable.draggable('option', 'revert', false);
    correctSuit++;
    if(correctSuit === 4) {
      nextGame();
    }
  }
}
///GAME ANSWERS///
var answerHolder = $('.answer-list'),
    answerList = answerHolder.find('li').detach(),
    usedAnswerList = [],
    newAnswerList = [];
    while (true) {
      var liAnswer = answerList[Math.floor(Math.random() * answerList.length)];
      if (usedAnswerList.indexOf(liAnswer) === -1 && newAnswerList.indexOf(liAnswer) === -1) newAnswerList.push(liAnswer);
      if (newAnswerList.length >= 3) break;
    }
    usedAnswerList = newAnswerList;
    answerHolder.html(newAnswerList);
$('.answer-list li').click(function() {
  var answer = $(this).data('answer');
  if (answer == 2) {
    $(this).addClass('correct');
    nextGame();
  } else {
    $(this).addClass('wrong');
    wrongPlay();
  }
});

$('#soundFix').click(function() {
  $('#correct-audio').get(0).play();
  $('#wrong-audio').get(0).play();

  // nextGame();
});
function wrongPlay() {
  $("#wrong-audio").get(0).muted = false;
  $('#wrong-audio').get(0).play();
}
function correctPlay() {
  $("#correct-audio").get(0).muted = false;
  $('#correct-audio').get(0).play();
}
function nextGame() {
  if(game < 5) {
    correctPlay();
    $('#game-'+game).fadeOut();
    game++;
    $('.correct-pop').fadeIn(function(){


    }).delay(300).fadeOut(function(){
      $('#game-'+game).slideDown();
      $('.game-status span').text(game);
    });




  } else {
    $('.loader').fadeIn(function() {
      $('.game-time span').stopwatch().stopwatch('stop');
      $('.result').val($('.game-time span').text());
      $('#formEnd').submit();
    });
  }
}
$(document).ready(function() {
    $('.loader').delay(1000).fadeOut(function() {
      function handleTimer() {
        if(count === 0) {
          $('.loader-start').fadeOut();
          clearInterval(timer);
          endCountdown();
        } else {
          $('.loader-start-num').html(count);
          count--;
        }
      }

      var count = 2;
      var timer = setInterval(function() { handleTimer(count); }, 1000);
    });

    function endCountdown() {
      $('.game-time span').stopwatch().stopwatch('start');
    }



    var audioInit = 0;
    document.body.addEventListener("mousemove", function () {
      if(audioInit == 0) {

        $("#correct-audio").get(0).muted = true;
        $("#correct-audio").get(0).play();
        $("#wrong-audio").get(0).muted = true;
        $("#wrong-audio").get(0).play();
        audioInit = 1;
      }
    })
    if (window.matchMedia("(orientation: landscape)").matches) {
       // alert('landscape');
    }
});
