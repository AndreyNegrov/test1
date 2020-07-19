ConstructorTraining = function (finishTrainingCallback) {
    Training.call(this, finishTrainingCallback);

    this.trainingName = 'ConstructorTraining';
    this.iteration = ConstructorTrainingIteration;

    this.buildDOM = () => {
        return {
            highSectionDOM: document.getElementById('high-section'),
            footerSectionDOM: document.getElementById('footer-section')
        }
    };
};

const ConstructorTrainingIteration = function (item, dom, finishIterationCallable) {
    TrainingIteration.call(this, item, dom, finishIterationCallable);

    this.guessedPartWord = [];

    this.fillDOM = () => {

        DOMModifier.setCartPicture(this.item.picture);
        DOMModifier.setMiddleSection(this.item.word);
        DOMModifier.setTranscription(this.item.transcription);

        this.DOM.highSectionDOM.innerHTML = '';
        this.DOM.footerSectionDOM.innerHTML = '';
        this.guessedPartWord = [];

        let arrayLitters = srtToArray(this.item.english);
        shuffle(arrayLitters).forEach((litter) => {
            //Рисуем ячейки для букв
            const elem = document.createElement('div');
            elem.setAttribute('class', 'result-word-letter');
            this.DOM.highSectionDOM.appendChild(elem);
            //Рисуем буквы
            const button = document.createElement('div');
            button.setAttribute('class', 'card__letter');
            button.innerHTML = litter;
            const div = document.createElement('div');
            div.setAttribute('class', 'card__letter-wrap');
            div.appendChild(button);
            this.DOM.footerSectionDOM.appendChild(div);
        });

        this.DOM.audioPlayerDOM.setAttribute(
            'src',
            'https://mister-teacher.com/words/voices/' + this.item.english.replace('&#039;', '\'') + '.mp3'
        );
    };

    this.addEventListeners = () => {
        this.DOM.footerSectionDOM.childNodes.forEach((wrapLitter) => {
            wrapLitter.addEventListener("click", this.chooseLitter);
        });
    };

    this.chooseLitter = (event) => {
        DOMModifier.removeErrorContainer();
        const button = event.target;
        const wrapButton = button.parentNode;
        const litter = button.innerHTML;
        if (!this.validationAnswer(litter, button)) {
            return false;
        }
        this.setAnswer(litter, wrapButton);
    };

    this.setAnswer = (litter, wrapButton) => {
        wrapButton.innerHTML = '';
        this.guessedPartWord.push(litter);
        const letterCells = this.DOM.highSectionDOM.childNodes;
        this.guessedPartWord.forEach((litter, i) => {
            letterCells[i].innerHTML = litter;
        });

        if (this.item.english.length === this.guessedPartWord.length) {
            this.setIterationToEnd();
        }

        wrapButton.removeEventListener("click", this.chooseLitter);
    };

    this.setIterationToEnd = () => {
        DOMModifier.setFinishContainer();
        this.DOM.audioPlayerDOM.play();
    };

    this.validationAnswer = (litter, button) => {
        let success = true;

        if (this.item.english[this.guessedPartWord.length] !== litter) {
            this.causeError();
            button.classList.add("error-word");
            success = false;
        }

        return success;
    };

    this.gameOver = () => {
        this.DOM.footerSectionDOM.childNodes.forEach((word) => {
            word.removeEventListener("click", this.chooseLitter);
        });

        this.DOM.highSectionDOM.innerHTML = '';
        let arrayLitters = srtToArray(this.item.english);
        arrayLitters.forEach((litter) => {
            const elem = document.createElement('div');
            elem.setAttribute('class', 'result-word-letter');
            elem.innerHTML = litter;
            this.DOM.highSectionDOM.appendChild(elem);
        });
    }
};
