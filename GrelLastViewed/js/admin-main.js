function changeLang(name, value)
{
var l = window.location;
var params = {};        
var x = /(?:\??)([^=&?]+)=?([^&?]*)/g;        
var s = l.search;
for(var r = x.exec(s); r; r = x.exec(s))
{
    r[1] = decodeURIComponent(r[1]);
    if (!r[2]) r[2] = '%%';
    params[r[1]] = r[2];
}
params[name] = encodeURIComponent(value);
var search = [];
for(var i in params)
{
    var p = encodeURIComponent(i);
    var v = params[i];
    if (v != '%%') p += '=' + v;
    search.push(p);
}
search = search.join('&');
l.search = search;
}