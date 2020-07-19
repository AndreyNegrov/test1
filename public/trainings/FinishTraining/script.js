FinishTraining = function (finishTrainingCallback) {
    Training.call(this, finishTrainingCallback);

    this.trainingName = 'FinishTraining';
    this.iteration = FinishTrainingIteration;

    this.buildDOM = () => {
        return {
            highSectionDOM: document.getElementById('high-section')
        }
    };
};

const FinishTrainingIteration = function (item, dom, finishIterationCallable) {
    TrainingIteration.call(this, item, dom, finishIterationCallable);

    this.fillDOM = () => {

        unique(trainingHandler.errorWords).forEach((word) => {
            const wWord = document.createElement('div');
            wWord.setAttribute('class', 'red-word');
            wWord.innerHTML = word;

            const icon = document.createElement('div');
            icon.setAttribute('class', 'fail-icon');

            const item = document.createElement('div');
            item.setAttribute('class', 'word-item');
            item.append(icon);
            item.append(wWord);

            this.DOM.highSectionDOM.append(item);
        });

        const successItems = [];

        trainingHandler.items.forEach((word) => {
            if (trainingHandler.errorWords.indexOf(word.english) !== -1) {
                return;
            }

            successItems.push(word.id);

            const wWord = document.createElement('div');
            wWord.setAttribute('class', 'green-word');
            wWord.innerHTML = word.english;

            const icon = document.createElement('div');
            icon.setAttribute('class', 'success-icon');

            const item = document.createElement('div');
            item.setAttribute('class', 'word-item');
            item.append(icon);
            item.append(wWord);

            this.DOM.highSectionDOM.append(item);
        });

        const xhr = new XMLHttpRequest();

        const body = 'session_id=' + trainingHandler.session + '&success_items_id=' + successItems;

        xhr.open('POST', '/training/finish');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(body);


    };

    this.addEventListeners = () => {};

    this.gameOver = () => {}
};
