RussianContextTraining = function (finishTrainingCallback) {
    Training.call(this, finishTrainingCallback);

    this.trainingName = 'RussianContextTraining';

    this.iteration = RussianContextTrainingIteration;

    this.buildDOM = () => {
        return {
            highSectionDOM: document.getElementById('high-section'),
            footerSectionDOM: document.getElementById('footer-section')
        }
    };
};

const RussianContextTrainingIteration = function (item, dom, finishIterationCallable) {
    TrainingIteration.call(this, item, dom, finishIterationCallable);

    this.context = [];

    this.fillDOM = () => {

        this.context = this.item.additionInfo.examples[0].russian.split(' ');

        DOMModifier.setCartPicture(this.item.picture);
        DOMModifier.setMiddleSection(this.item.additionInfo.examples[0].english);

        this.DOM.highSectionDOM.innerHTML = '';
        this.DOM.footerSectionDOM.innerHTML = '';

        shuffle(this.item.additionInfo.examples[0].russian.split(' ')).forEach((word) => {
            const elem = document.createElement('div');
            elem.setAttribute('class', 'result-word');
            this.DOM.highSectionDOM.appendChild(elem);

            const button = document.createElement('div');
            button.setAttribute('class', 'choosing-word');
            button.innerHTML = word;
            const div = document.createElement('div');
            div.setAttribute('class', 'choosing-word-wrap');
            div.appendChild(button);
            this.DOM.footerSectionDOM.appendChild(div);
        });

        this.DOM.audioPlayerDOM.setAttribute(
            'src',
            'https://mister-teacher.com/words/voices/' + this.item.additionInfo.examples[0].english.replace('&#039;', '\'') + '.mp3'
        );
        this.DOM.audioPlayerDOM.play();
    };

    this.addEventListeners = () => {

        this.DOM.footerSectionDOM.childNodes.forEach((wrapLitter) => {
            wrapLitter.addEventListener("click", this.chooseWord);
        });

        this.DOM.highSectionDOM.childNodes.forEach((word) => {
            word.addEventListener("click", this.removeWord);
        });
    };

    this.removeHighSectionDOMEventListeners = () => {
        this.DOM.highSectionDOM.childNodes.forEach((word) => {
            word.removeEventListener("click", this.removeWord);
        });
    };

    this.removeWord = (event) => {
        const resultWordContainer = event.target;
        try {
            this.DOM.footerSectionDOM.childNodes.forEach((wordContainer) => {
                if (wordContainer.childNodes[0].innerHTML === resultWordContainer.innerHTML) {
                    wordContainer.style.visibility = 'visible';
                    wordContainer.addEventListener("click", this.chooseWord);
                    throw {}
                }
            });
        } catch (e) {
        }

        resultWordContainer.innerHTML = '';
        resultWordContainer.style.width = '';
    };

    this.chooseWord = (event) => {
        const button = event.target;
        const wrapButton = button.parentNode;
        const word = button.innerHTML;
        wrapButton.style.visibility = 'hidden';
        DOMModifier.removeErrorContainer();

        let countFree = 0;
        let putFlag = false;

        this.DOM.highSectionDOM.childNodes.forEach((wordContainer, index) => {
            if (!wordContainer.innerHTML) {
                countFree++;
                if (!putFlag) {
                    wordContainer.innerHTML = word;
                    wordContainer.style.width = 'auto';
                    putFlag = true;
                }
            }
        });

        if (countFree < 2 && this.validationAnswer()) {
            this.setIterationToEnd();
        }
    };

    this.setIterationToEnd = () => {
        this.removeHighSectionDOMEventListeners();
        DOMModifier.setFinishContainer();
    };

    this.validationAnswer = () => {

        let success = true;

        this.DOM.highSectionDOM.childNodes.forEach((word, index) => {
            if (this.context[index].replace('&#039;', '\'') !== word.innerHTML) {
                success = false;

                this.DOM.footerSectionDOM.childNodes.forEach((wordContainer) => {
                    if (wordContainer.childNodes[0].innerHTML === word.innerHTML) {
                        wordContainer.style.visibility = 'visible';
                    }
                });

                word.innerHTML = '';
                word.style.width = '';
            }
        });

        if (!success) {
            this.causeError();
        }

        return success;
    };

    this.gameOver = () => {


        this.DOM.footerSectionDOM.childNodes.forEach((wordContainer) => {
            wordContainer.style.visibility = 'hidden';
        });

        this.removeHighSectionDOMEventListeners();

        this.DOM.highSectionDOM.childNodes.forEach((word, index) => {
            if (word.innerHTML === '') {
                word.style.color = 'red';
                word.innerHTML = this.context[index].replace('&#039;', '\'');
                word.style.width = 'auto';
            }
        });
    }

};
