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
