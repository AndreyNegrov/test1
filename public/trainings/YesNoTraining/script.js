YesNoTraining = function (finishTrainingCallback) {

    Training.call(this, finishTrainingCallback);

    this.trainingName = 'YesNoTraining';
    this.iteration = YesNoTrainingIteration;

    this.buildDOM = () => {
        return {
            wrongButtonDOM: document.getElementById('wrong-button'),
            rightButtonDOM: document.getElementById('right-button'),
            successPlayerDOM: document.getElementById('success-player'),
        }
    };
};

const YesNoTrainingIteration = function (item, dom, finishIterationCallable) {
    TrainingIteration.call(this, item, dom, finishIterationCallable);

    this.showedWord = null;

    this.fillDOM = () => {
        DOMModifier.removeErrorContainer();
        document.getElementById('train-container').classList.remove("bad-word");
        DOMModifier.setHighSection(this.item.english);

        document.getElementsByClassName('right-word')[0].innerHTML = this.item.word;
        if (parseInt(Math.random() * 10) % 2) {
            DOMModifier.setMiddleSection(this.item.word);
            this.showedWord = this.item.word;
        } else {
            const elem = trainingHandler.items[Math.floor(Math.random() * trainingHandler.items.length)];
            DOMModifier.setMiddleSection(elem.word);
            this.showedWord = elem.word;
            document.getElementsByClassName('wrong-word')[0].innerHTML = elem.word;
        }

        DOMModifier.setCartPicture(this.item.picture);
        this.DOM.successPlayerDOM.volume = 0.5;
    };

    this.addEventListeners = () => {
        this.rightButton = this.validationAnswer.bind(null, true);
        this.wrongButton = this.validationAnswer.bind(null, false);
        this.DOM.rightButtonDOM.addEventListener("click", this.rightButton);
        this.DOM.wrongButtonDOM.addEventListener("click", this.wrongButton);
    };

    this.removeEventListeners = () => {
        this.DOM.rightButtonDOM.removeEventListener("click", this.rightButton);
        this.DOM.wrongButtonDOM.removeEventListener("click", this.wrongButton);
    };

    this.validationAnswer = (value) => {

        let flag = true;

        if (this.showedWord !== this.item.word) {
            flag = false;
            document.getElementById('train-container').classList.add("bad-word");
        }

        if (flag !== value) {
            this.causeError();
        } else {
            this.DOM.successPlayerDOM.play();
        }

        this.removeEventListeners();
        DOMModifier.setFinishContainer();
    };


    this.gameOver = () => {
    }
};
