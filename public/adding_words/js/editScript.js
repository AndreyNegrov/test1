
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