
$('#english-word').keyup(function(event){
    if(event.keyCode === 13){
        searchWord();
    }
});

function searchWord() {

    $('.info-block').hide();
    $('.spinner-container').show();
    const englishWordElement = $('#english-word');
    const englishWord = englishWordElement.val().toLowerCase().trim();
    englishWordElement.val(englishWord);

    $.get('/adding_words/getWordInfo/' + englishWord, function (data) {
        $('#voice').attr('src', '/words/voices/' + englishWord + '.mp3').show();
        window.wordInfo = JSON.parse(data['data'])['def'];
        drawPartSpeech();
        $('.info-block').show();
        $('.spinner-container').hide();
        fillCardFields();
    });
}

function drawPartSpeech() {
    const partSpeech = $('#part-speech');
    partSpeech.html('');
    window.wordInfo.forEach(function (item) {
        partSpeech.append('<option value="' + item.pos + '">' + mappingPartSpeech(item.pos) + '</option>');
    });
    changePartSpeech();
}

function mappingPartSpeech(en) {
    const map = {
        'noun': 'существительное',
        'participle': 'причастие',
        'verb': 'глагол',
        'adjective': 'прилагательное',
        'numeral': 'числительное',
        'adverb': 'наречие',
        'preposition': 'предлог',
        'pronoun': 'местоимение',
        'conjunction': 'союз',
        'particle': 'частица'

    };
    return map.hasOwnProperty(en) ? map[en] : en;
}

function changePartSpeech() {
    const partSpeech = $('#part-speech');
    const changedPartSpeech = window.wordInfo.find((element) => element.pos === partSpeech.val());
    if (changedPartSpeech && changedPartSpeech.hasOwnProperty('tr')) {
        window.wordTranslations = changedPartSpeech.tr;
        drawTranscription(changedPartSpeech.hasOwnProperty('ts') ? changedPartSpeech.ts : '');
        drawTranslate(changedPartSpeech);
    } else {
        alert('Перевод не найден(((');
    }
}

function drawTranscription(transcription) {
    $('#transcription-input').val(transcription);
}

function drawTranslate(changedPartSpeech) {
    const translateSelect = $('#translate-select');
    translateSelect.html('');
    window.wordTranslations.forEach(function (item) {
        translateSelect.append('<option value="' + item.text + '">' + item.text + '</option>');
    });
    changeTranslate();
}

function changeTranslate() {
    const translateSelect = $('#translate-select');
    const changedTranslateSelect = window.wordTranslations.find((element) => element.text === translateSelect.val());
    drawSynonyms(changedTranslateSelect.hasOwnProperty('syn') ? changedTranslateSelect.syn : []);
    drawEnglishSynonyms(changedTranslateSelect.hasOwnProperty('mean') ? changedTranslateSelect.mean : []);
    drawExamples(changedTranslateSelect.hasOwnProperty('ex') ? changedTranslateSelect.ex : []);
    changeTranslation();
}

function drawSynonyms(synonyms) {
    const synonymsInputs = $('#synonyms-inputs');
    synonymsInputs.html('');
    synonyms.forEach(function (item) {
        synonymsInputs.append("<div class='input-group mb-2'>" +
            '<input class="form-control si" name="russian-synonyms[]" value="' + item.text + '">' +
            '<div class="input-group-prepend"><div class="btn btn-danger" onclick="removeElem(this)">-</div></div>' +
            "</div>");
    });
}

function drawEnglishSynonyms(synonyms) {
    const synonymsInputs = $('#english-synonyms-inputs');
    synonymsInputs.html('');
    synonyms.forEach(function (item) {
        synonymsInputs.append("<div class='input-group mb-2'>" +
            '<input class="form-control si" name="english-synonyms[]" value="' + item.text + '">' +
            '<div class="input-group-prepend"><div class="btn btn-danger" onclick="removeElem(this)">-</div></div>' +
            "</div>");
    });
}

function drawExamples(examples) {
    const examplesInputs = $('#examples-inputs');
    examplesInputs.html('');
    examples.forEach(function (item, i) {
        let br = i !== 0 ? '<br>' : '';
        examplesInputs.append("<div>" + br +
            "<div class='input-group mb-2'>" +
            '<input class="form-control si" name="english-examples[]" value="' + item.text + '">' +
            '<div class="input-group-prepend"><div class="btn btn-danger" onclick="removeExElem(this)">-</div></div>' +
            '</div>' +
            '<input class="form-control si" name="russian-examples[]" value="' + item.tr[0].text + '">' +
            '</div>');
    });
}

function removeElem(elem) {
    $(elem).parent().parent().remove();
}
function removeExElem(elem) {
    $(elem).parent().parent().parent().remove();
}

function addSynonyms() {
    const synonymsInputs = $('#synonyms-inputs');
    synonymsInputs.append("<div class='input-group mb-2'>" +
        '<input class="form-control si" name="russian-synonyms[]">' +
        '<div class="input-group-prepend"><div class="btn btn-danger" onclick="removeElem(this)">-</div></div>' +
        "</div>");
}

function addEnglishSynonyms() {
    const synonymsInputs = $('#english-synonyms-inputs');
    synonymsInputs.append("<div class='input-group mb-2'>" +
        '<input class="form-control si" name="english-synonyms[]">' +
        '<div class="input-group-prepend"><div class="btn btn-danger" onclick="removeElem(this)">-</div></div>' +
        "</div>");
}

function addExamplesInputs() {
    const examplesInputs = $('#examples-inputs');
    let br = examplesInputs.html() !== '' ? '<br>' : '';
    examplesInputs.append("<div>" + br +
        "<div class='input-group mb-2'>" +
        '<input class="form-control si" name="english-examples[]">' +
        '<div class="input-group-prepend"><div class="btn btn-danger" onclick="removeExElem(this)">-</div></div>' +
        '</div>' +
        '<input class="form-control si" name="russian-examples[]">' +
        '</div>');
}

function submitForm() {
    document.getElementById('form-new-word').submit();
}

function blockWord(id, elem) {
    console.log(elem);
    $.get('/edit_words/block/' + id, function (data) {
        elem.style.display = 'none';
        $(elem).parent().find('.unblock-button').css('display', 'inline-block');
    });
}

function unblockWord(id, elem) {
    console.log(elem);
    $.get('/edit_words/unblock/' + id, function (data) {
        elem.style.display = 'none';
        $(elem).parent().find('.block-button').css('display', 'inline-block');
    });
}