const express = require('express');
const app = express();

app.use(express.static('static_files'));

const sqlite3 = require('sqlite3');
const db = new sqlite3.Database('beers.db');

const bodyParser = require('body-parser');
app.use(bodyParser.urlencoded({extended: true}));


app.get('/beers', (req, res) => {
  db.all('SELECT id, name, rating, ratingcount FROM saved_beers', (err, rows) => {
  	const allBeers = rows;
  	console.log(allBeers)
	res.send(allBeers);
  });
});

app.post('/beers', (req, res) => {
  console.log(req.body);
  db.run(
    'INSERT INTO saved_beers VALUES ($id, $name, $rating, $ratingcount)',
    {
      $id: req.body.id,
      $name: req.body.name,
      $rating: req.body.rating,
      $ratingcount: req.body.ratingcount,
    },
    (err) => {
      if(err){
        res.send({message: 'erroe in server.js'});
      } else {
        res.send({message: 'successfully posted!'});
      }
    }
  );
});

var server = app.listen(3000, '192.168.111.151', () => {
  console.log('Server started at http://localhost:3000/');
});
