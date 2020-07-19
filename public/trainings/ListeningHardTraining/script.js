ListeningHardTraining = function (finishTrainingCallback) {
    Training.call(this, finishTrainingCallback);

    this.trainingName = 'ListeningHardTraining';
    this.iteration = ListeningHardTrainingIteration;

    this.buildDOM = () => {
        return {
            resultWordDOM: document.getElementsByClassName('result-word-input')[0],
            cardDOM: document.getElementsByClassName('card-keyboard')[0],
            confirmButtonDOM: document.getElementsByClassName('confirm-button')[0],
            rightWordDOM: document.getElementsByClassName('right-word')[0],
            wrongWordDOM: document.getElementsByClassName('wrong-word')[0],
            replyButtonDOM: document.getElementsByClassName('reply-button')[0],
            answerContainerDOM: document.getElementsByClassName('answer-container')[0]
        }
    };
};

const ListeningHardTrainingIteration = function (item, dom, finishIterationCallable) {
    TrainingIteration.call(this, item, dom, finishIterationCallable);

    this.DOM.cardDOM.style.height = document.body.clientHeight * 0.53;

    this.fillDOM = () => {

        this.DOM.resultWordDOM.value = '';
        this.resizeInput();
        DOMModifier.setCartPicture(this.item.picture);
        DOMModifier.setMiddleSection(this.item.word);
        DOMModifier.setTranscription(this.item.transcription);

        this.DOM.audioPlayerDOM.setAttribute(
            'src',
            'https://mister-teacher.com/words/voices/' + this.item.english.replace('&#039;', '\'') + '.mp3'
        );

        DOMModifier.playTranscription();

        this.DOM.resultWordDOM.focus();
        this.DOM.resultWordDOM.click();
    };

    this.addEventListeners = () => {
        this.DOM.resultWordDOM.addEventListener("keydown", this.resizeInput);
        this.DOM.confirmButtonDOM.addEventListener("click", this.confirmWord);
        this.DOM.replyButtonDOM.addEventListener("click", this.replyAfterError);
    };

    this.resizeInput = () => {
        this.DOM.resultWordDOM.style.width = this.DOM.resultWordDOM.value.length > 2 ? this.DOM.resultWordDOM.value.length * 2 + 30 + '%' : '30%';
    };

    this.confirmWord = (event) => {

        if (!this.validationAnswer()) {
            return false;
        }
        this.setIterationToEnd();
    };

    this.validationAnswer = (litter, button) => {
        let success = true;

        const result = this.DOM.resultWordDOM.value.toLowerCase().trim();

        if (result === '') {
            this.DOM.resultWordDOM.focus();
            this.DOM.resultWordDOM.click();
            return false;
        }

        if (result !== this.item.english) {
            this.causeError();
            this.DOM.rightWordDOM.innerHTML = this.item.english;
            this.DOM.wrongWordDOM.innerHTML = result;
            success = false;
        }

        return success;
    };

    this.replyAfterError = () => {
        DOMModifier.removeErrorContainer();
        this.DOM.resultWordDOM.focus();
        this.DOM.resultWordDOM.click();
    };

    this.setIterationToEnd = () => {
        DOMModifier.setFinishContainer();
        this.DOM.answerContainerDOM.innerHTML = this.item.english;
    };

    this.gameOver = () => {
        this.DOM.replyButtonDOM.style.visibility = 'hidden';
    }
};
