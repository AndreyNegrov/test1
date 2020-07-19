TrainingHandler = function (session, items, trainings) {

    this.trainings = trainings ? trainings : ['TranslateWordTraining', 'ConstructorTraining', 'ListeningContextTraining', 'WordTranslateTraining', 'ListeningTraining', 'ExplainTraining', 'ContextTraining', 'SynonymTraining'];
    this.trainingIndex = -1;

    this.session = session;
    this.items = [];
    this.windowSize = 0;
    this.nextTrainingHtml = null;
    this.nextTrainingSrc = false;

    items.forEach((item) => {
        this.items.push(
            {
                id: item.id,
                transcription: item.transcription,
                english: item.english.toLocaleLowerCase(),
                word: item.word,
                picture: item.picture,
                status: 0,
                additionInfo: JSON.parse(item.additionInfo.replace(/&quot;/g, '"'))
            }
        );
        this.life = 3;
        this.maxLife = 3;
        this.errorWords = [];
    });

    this.nextTraining = () => {
        if (this.trainingIndex === this.trainings.length - 1) {
            this.downloadTraining('FinishTraining');
        } else {
            this.trainingIndex++;
            this.downloadTraining(this.trainings[this.trainingIndex], this.trainingIndex === this.trainings.length - 1 ? 'FinishTraining' : this.trainings[this.trainingIndex + 1]);
        }
    };

    this.downloadTraining = (name, nextTrainingName) => {

        const self = this;

        new Promise(function (resolve) {
            let script;
            if (self.nextTrainingSrc && name !== 'GameOver') {
                self.nextTrainingSrc = false;
                resolve();
            } else {
                script = document.createElement('script');
                script.src = '/trainings/' + name + '/script.js';
                script.onload = () => resolve();
                document.head.append(script);
            }
        }).then(() => {
            const training = eval('new ' + name + '(this.nextTraining)');
            training.init(nextTrainingName);
        });
    };

    this.gameOver = (card) => {
        this.cartType = card.length > 0 ? 'card' : 'card-keyboard';
        this.downloadTraining('GameOver');
    }
};

TrainingIteration = function (dom, finishIterationCallable) {

    this.item = null;
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
        this.downloadNextTrainingElements();
    };

    this.downloadNextTrainingElements = () => {

        let nextItem = null;

        for (let i = 0; i < trainingHandler.items.length - 1; i++) {
            if (trainingHandler.items[i].id == this.item.id) {
                nextItem = trainingHandler.items[i + 1];
                break;
            }
        }

        if (!nextItem) return;

        let elemImg = document.getElementsByClassName('next-image');
        let elemAudio = document.getElementsByClassName('next-audio');
        let audio = null;
        let img = null;

        if (!elemImg.length) {
            img = document.createElement('img');
            img.setAttribute('class', 'next-image');
            document.body.append(img);
        } else {
            img = elemImg[0];
            audio = elemAudio[0];
        }
        img.src = nextItem.picture;

        if (this.DOM.audioPlayerDOM) {
            if (!elemAudio.length) {
                audio = document.createElement('audio');
                audio.setAttribute('class', 'next-audio');
                this.DOM.audioPlayerDOM.parentNode.insertBefore(audio, this.DOM.audioPlayerDOM.nextSibling);
            } else {
                audio = elemAudio[0];
            }
            audio.setAttribute(
                'src',
                'https://mister-teacher.com/words/voices/' + nextItem.english.replace('&#039;', '\'') + '.mp3'
            );
        }

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
        this.DOM.nextButtonDOM.addEventListener("click", trainingHandler.gameOver.bind(this, document.getElementsByClassName('card')));
        this.DOM.errorPlayerDOM.setAttribute('src', '../../trainings/GameOver/sounds/gameOver2.mp3');
        this.DOM.errorPlayerDOM.volume = 0.3;
        this.DOM.errorPlayerDOM.play();
    };

};

Training = function (finishTrainingCallback) {

    this.finishTrainingCallback = finishTrainingCallback;
    this.items = makeIterator(trainingHandler.items);

    this.testIteration = {};

    this.init = (nextTrainingName) => {

        if (!trainingHandler.nextTrainingHtml) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '/trainings/' + this.trainingName + '/index2.html', true);
            xhr.send();
            xhr.onreadystatechange = () => {
                if (xhr.readyState !== 4) return;
                if (xhr.status === 200) {
                    this.abc(xhr.responseText, nextTrainingName);
                }
            };
        } else {
            this.abc(trainingHandler.nextTrainingHtml, nextTrainingName);
        }
    };

    this.abc = (htmlResponse, nextTrainingName) => {
        trainingHandler.nextTrainingHtml = null;
        document.body.innerHTML = htmlResponse;
        DOMModifier.setTrainingNumber();
        DOMModifier.setLife();
        this.DOM = this.buildDOM();
        this.testIteration = new this.iteration(this.DOM, this.nextIteration);
        this.testIteration.next(this.items.next());
        this.downloadNextTrainingInBackground(nextTrainingName);
    };

    this.downloadNextTrainingInBackground = (nextTrainingName) => {
        const script = document.createElement('script');
        script.src = '/trainings/' + nextTrainingName + '/script.js';
        document.head.append(script);
        trainingHandler.nextTrainingSrc = true;

        // const elem = document.createElement('src');
        // elem.setAttribute('src', 'result-word-letter');
        // document.body.append(elem);

        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/trainings/' + nextTrainingName + '/index2.html', true);
        xhr.send();
        xhr.onreadystatechange = () => {
            if (xhr.readyState !== 4) return;
            if (xhr.status === 200) {
                trainingHandler.nextTrainingHtml = xhr.responseText;
            }
        };


    };

    this.nextIteration = () => {
        const item = this.items.next();
        if (item) {
            this.testIteration.next(item);
        } else {
            this.finishTraining();
        }
    };

    this.finishTraining = () => {
        document.body.innerHTML = '';
        this.finishTrainingCallback();
    };
};

DOMModifier = {
    setTrainingNumber: () => {
        document.getElementById('training-number').innerHTML = (trainingHandler.trainingIndex + 1) + '/' + trainingHandler.trainings.length;
    },

    setLife: () => {
        const lifeContainerDOM = document.getElementById('life-container');
        lifeContainerDOM.innerHTML = '';
        for (let i = 0; i < trainingHandler.maxLife; i++) {
            const elem = document.createElement('img');
            elem.setAttribute('class', 'heart');

            i < trainingHandler.life
                ? elem.setAttribute('src', '../../trainings/GameOver/images/fullHeart.jpg')
                : elem.setAttribute('src', '../../trainings/GameOver/images/emptyHeart.jpg');
            lifeContainerDOM.append(elem);
        }
    },

    setCartPicture: (pathPicture) => {
        document.getElementById('card-picture').setAttribute('src', pathPicture);
    },

    setProcessContainer: () => {
        document.getElementById('train-container').classList.remove("finish-word");
        document.getElementById('train-container').classList.add("process-word");
    },

    setFinishContainer: () => {
        document.getElementById('train-container').classList.remove("process-word");
        document.getElementById('train-container').classList.add("finish-word");
    },

    setErrorContainer: () => {
        document.getElementById('train-container').classList.add("error-word");
    },

    removeErrorContainer: () => {
        const elements = document.getElementsByClassName("error-word");
        while (elements.length) {
            elements[0].classList.remove('error-word');
        }
    },

    setMiddleSection: (value) => {
        document.getElementById('middle-section').innerHTML = value;
    },

    setHighSection: (value) => {
        document.getElementById('high-section').innerHTML = value;
    },

    setTranscription: (value) => {
        document.getElementById('transcription').innerHTML = "[" + value + "]";
    },

    playTranscription: () => {
        document.getElementsByClassName('audio-wrap')[0].style.background = '#d9d8d8';
        document.getElementById('audio-player').play();
        setTimeout(() => {
            document.getElementsByClassName('audio-wrap')[0].style.background = '';
        }, 500);
    }
};

function makeIterator(array) {
    let nextIndex = 0;

    return {
        next: function () {
            return nextIndex < array.length ? array[nextIndex++] : null;
        }
    }
}

function srtToArray(string) {
    const $array = [];
    for (let i = 0; i < string.length; i++) {
        $array.push(string[i]);
    }
    return $array;
}

function shuffle(arr) {
    let j, temp;
    for (let i = arr.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        temp = arr[j];
        arr[j] = arr[i];
        arr[i] = temp;
    }
    return arr;
}

function unique(arr) {
    let result = [];

    for (let str of arr) {
        if (!result.includes(str)) {
            result.push(str);
        }
    }

    return result;
}
