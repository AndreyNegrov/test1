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