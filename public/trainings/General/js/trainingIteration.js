TrainingIteration = function (dom, finishIterationCallable) {

    this.item = [];
    this.DOM = dom;
    this.DOM.nextButtonDOM = document.getElementById('next-button');
    this.DOM.errorPlayerDOM = document.getElementById('error-player');
    this.DOM.audioWrapDOM = document.getElementsByClassName('audio-wrap')[0];
    this.DOM.audioPlayerDOM = document.getElementById('audio-player');

    this.next = (item) => {
        this.item = item;
        this.fillDefaultDOM();
        this.fillDOM();
        this.addDefaultEventListeners();
        this.addEventListeners();
    };

    this.fillDefaultDOM = () => {
        DOMModifier.setProcessContainer();
    };

    this.addDefaultEventListeners = () => {
        this.DOM.nextButtonDOM.addEventListener("click", this.finishIteration);
        if (this.DOM.audioWrapDOM) {
            this.DOM.audioWrapDOM.addEventListener("click", DOMModifier.playTranscription);
        }
    };

    this.removeDefaultEventListeners = () => {
    };

    this.finishIteration = () => {
        this.removeDefaultEventListeners();
        finishIterationCallable();
    };

    this.causeError = () => {
        trainingHandler.life--;
        DOMModifier.setErrorContainer();
        DOMModifier.setLife();
        if (trainingHandler.life === 0) {
            this.gameOverDefault();
            this.gameOver();
        } else {
            this.DOM.errorPlayerDOM.play();
            trainingHandler.errorWords.push(this.item.english);
        }
    };

    this.gameOverDefault = () => {
        DOMModifier.setFinishContainer();
        this.DOM.nextButtonDOM.removeEventListener("click", this.finishIteration);
        this.DOM.nextButtonDOM.addEventListener("click", GameOver.bind(this, document.getElementsByClassName('card')));
        this.DOM.errorPlayerDOM.setAttribute('src', '../../trainings/GameOver/sounds/gameOver2.mp3');
        this.DOM.errorPlayerDOM.volume = 0.3;
        this.DOM.errorPlayerDOM.play();
    };

};
