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

function unique (arr) {
    let result = [];

    for (let str of arr) {
        if (!result.includes(str)) {
            result.push(str);
        }
    }

    return result;
}