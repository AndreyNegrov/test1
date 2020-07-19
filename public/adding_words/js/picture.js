$(function () {
    $('#R').slider().on('slide', changeEnglishWordContainer);
    $('.slide-transcription').slider().on('slide', changeTranscriptionPosition);

    $('#color-picker').colorpicker({
        popover: false,
        inline: true
    }).on('colorpickerChange', changeColor);

    $('#image-path').keyup(function (event) {
        if (event.keyCode === 13) {
            changePicture();
        }
    });
});

function changePicture() {
    $('.pictures-block').css('visibility', 'hidden');
    $('.spinner-image-container').show();
    const path = $('#image-path');
    var image = $('#pictures-crop');
    image.cropper({
        aspectRatio: 696 / 579,
        preview: '.pictures-markup',
        crop: function (event) {
        },
        autoCropArea: 1
    });

    $.ajax({
        url: "downloadImage",
        data: "url=" + path.val(),
        success: function (msg) {
            var cropper = image.data('cropper');
            cropper.replace('/words/tmp/abc.jpg');
            $('.spinner-image-container').hide();
            $('.pictures-block').css('visibility', 'visible');
        }
    });
}

function fillCardFields() {
    changeWord();
    changeTranscription();
    changeTranslation();
}

function changeWord() {
    let value = $('#english-word').val();
    const leftsTranscription = {
        '3': '85',
        '4': '133',
        '5': '178',
        '6': '220',
        '7': '278',
        '8': '281',
        '9': '325',
        '10': '367',
        '11': '367'
    };
    $('#pictures-word').html(value);
    $('#letter-transcription-position').css('left', leftsTranscription[value.length] + 'px');
    $('.slide-transcription').slider().slider('setValue', leftsTranscription[value.length]);
}

function changeTranscription() {
    let value = $('#transcription-input').val();
    $('#pictures-transcription').html(value);
}

function changeTranslation() {
    let value = $('#translate-select').val();
    $('#pictures-translate').html(value);
}

function changeEnglishWordContainer(event) {
    $('.english-word-container').css('width', event.value);
}

function changeTranscriptionPosition(event) {
    $('#letter-transcription-position').css('left', event.value);
}

function changeColor(event) {
    $('.letter-box').css('background-color', event.value);
    $('.translate-box').css('background-color', event.value);
}

function getCardImage() {
    $('.translate-letter').css('margin', '-21px 20px 15px 20px');
    const card = $('.card');
    return html2canvas(card[0], {
        scrollY: (window.pageYOffset * -1 - 2),
        scrollX: (window.pageXOffset * -1 - 11),
        width: 796,
        height: 604
    });
}

function getCropPicture() {
    return html2canvas($('.training-image')[0], {
        scrollY: 0,
        scrollX: -8
    });
}

function create() {
    getCardImage().then(canvas => {
        $('input[name="card"]').val(canvas.toDataURL());
        getCropPicture().then(canvas => {
            $('input[name="pictures"]').val(canvas.toDataURL());
            submitForm();
        });
    });
}


























