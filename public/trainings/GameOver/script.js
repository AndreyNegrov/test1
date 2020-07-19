GameOver = function() {

    this.trainingName = 'GameOver';

    this.init = () => {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '/trainings/' + this.trainingName + '/style.css';

        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/trainings/' + this.trainingName + '/index2.html', true);
        xhr.send();
        xhr.onreadystatechange = () => {
            if (xhr.readyState !== 4) return;
            if (xhr.status === 200) {
                document.body.innerHTML = xhr.responseText;

                document.getElementsByClassName('card-type')[0].classList.add(trainingHandler.cartType);

                document.head.append(link);
                this.setTrainingNumber();
            }
        };
    };

    this.setTrainingNumber = () => {
        document.getElementById('training-number').innerHTML = (trainingHandler.trainingIndex + 1) + '/' + trainingHandler.trainings.length
    };

};
