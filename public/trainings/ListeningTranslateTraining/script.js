ListeningTranslateTraining = function (finishTrainingCallback) {

    Training.call(this, finishTrainingCallback);

    this.trainingName = 'ListeningTranslateTraining';
    this.iteration = ListeningTranslateTrainingIteration;

    this.buildDOM = () => {
        return {
            middleSectionDOM: document.getElementById('middle-section')
        }
    };
};

const ListeningTranslateTrainingIteration = function (item, dom, finishIterationCallable) {
    TrainingIteration.call(this, item, dom, finishIterationCallable);

    this.fillDOM = () => {
        DOMModifier.setHighSection(this.item.english);
        DOMModifier.setTranscription(this.item.transcription);
        DOMModifier.setCartPicture(this.item.picture);

        const shuffledItems = shuffle(trainingHandler.items.slice());

        this.DOM.middleSectionDOM.innerHTML = '';

        shuffledItems.forEach((item) => {
            const elem = document.createElement('div');
            elem.setAttribute('class', 'button-word');
            elem.innerHTML = item.word;
            this.DOM.middleSectionDOM.appendChild(elem);
        });

        this.DOM.audioPlayerDOM.setAttribute(
            'src',
            'https://mister-teacher.com/words/voices/' + this.item.english + '.mp3'
        );

        DOMModifier.playTranscription();
    };

    this.addEventListeners = () => {
        this.DOM.middleSectionDOM.childNodes.forEach((wrapWord) => {
            wrapWord.addEventListener("click", this.chooseWord);
        });
    };

    this.removeEventListeners = () => {
        this.DOM.middleSectionDOM.childNodes.forEach((wrapWord) => {
            wrapWord.removeEventListener("click", this.chooseWord);
        });
    };

    this.chooseWord = (event) => {
        const button = event.target;
        const word = button.innerHTML;
        if (this.validationAnswer(word)) {
            button.classList.add("finish-word");
            this.setIterationToEnd();
        } else {
            button.classList.add("error-word");
        }
    };

    this.validationAnswer = (word) => {

        Array.from(document.getElementsByClassName('error-word')).forEach((element) => {
            element.classList.remove("error-word");
        });

        if (this.item.word !== word) {
            this.causeError();
            return false;
        }

        return true;
    };

    this.setIterationToEnd = () => {
        this.removeEventListeners();
        DOMModifier.setFinishContainer();
    };

    this.gameOver = () => {
        this.removeEventListeners();
        this.DOM.middleSectionDOM.childNodes.forEach((wrapWord) => {
            if (wrapWord.innerHTML === this.item.word) {
                wrapWord.classList.add("finish-word");
            }
        });
    }
};
