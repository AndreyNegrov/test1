RussianSynonymTraining = function (finishTrainingCallback) {

    Training.call(this, finishTrainingCallback);

    this.trainingName = 'RussianSynonymTraining';
    this.iteration = RussianSynonymIteration;

    this.buildDOM = () => {
        return {
            middleSectionDOM: document.getElementById('middle-section')
        }
    };
};

const RussianSynonymIteration = function (item, dom, finishIterationCallable) {
    TrainingIteration.call(this, item, dom, finishIterationCallable);

    this.fillDOM = () => {
        DOMModifier.setHighSection(this.item.additionInfo['russian-synonyms'].join(', '));
        DOMModifier.setTranscription(this.item.transcription);
        DOMModifier.setCartPicture(this.item.picture);

        const shuffledItems = shuffle(trainingHandler.items.slice());

        this.DOM.middleSectionDOM.innerHTML = '';

        shuffledItems.forEach((item) => {
            const elem = document.createElement('div');
            elem.setAttribute('class', 'button-word');
            elem.innerHTML = item.english;
            this.DOM.middleSectionDOM.appendChild(elem);
        });

        this.DOM.audioPlayerDOM.setAttribute(
            'src',
            'https://mister-teacher.com/words/voices/' + this.item.english.replace('&#039;', '\'') + '.mp3'
        );
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

        if (this.item.english !== word) {
            this.causeError();
            return false;
        }

        return true;
    };

    this.setIterationToEnd = () => {
        this.removeEventListeners();
        DOMModifier.setFinishContainer();
        this.DOM.audioPlayerDOM.play();
    };

    this.gameOver = () => {
        this.removeEventListeners();
        this.DOM.middleSectionDOM.childNodes.forEach((wrapWord) => {
            if (wrapWord.innerHTML === this.item.english) {
                wrapWord.classList.add("finish-word");
            }
        });
    }
};
