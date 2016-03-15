function getColorFromString(fullString) {
    fullString = fullString.replace(/\W+/g, '');
    var middleChar = fullString.substr(Math.floor(fullString.length / 2), 1);

    var colorNum = 1;
    switch(middleChar) {
        case 'b': case 'w': case 't': colorNum = 1; break;
        case 'd': case 'j': case 'r': colorNum = 2; break;
        case 'c': case 'l': case 'z': colorNum = 3; break;
        case 'e': case 'k': case 's': colorNum = 4; break;
        case 'f': case 'n': case 'p': colorNum = 5; break;
        case 'g': case 'm': case 'v': colorNum = 6; break;
        case 'u': case 'h': case 'i':  case 'y': colorNum = 7; break;
        case 'a': case 'o': case 'q':  case 'x': colorNum = 8; break;
    }

    return colorNum;
}

$(document).ready(function() {
    $(".button-collapse").sideNav();
});
