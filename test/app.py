from flask import Flask, render_template, request
import psycopg2
import datetime

app = Flask(__name__)

@app.route('/')
def hello_world():
    return '<h1>ZaLuPa KoNyA</h1><img src="https://cdn-st1.rtr-vesti.ru/vh/pictures/xw/217/453/4.jpg">'

@app.route('/db')
def test_db():
    try:
        page = request.args.get('page', default=1, type=int)
        conn = psycopg2.connect(dbname='test', user='admin', password='admin', host='pg_db', port='5432')
        cur = conn.cursor()
        cur.execute(f"SELECT * FROM articles ORDER BY pubdate DESC LIMIT 100 OFFSET {(page - 1) * 100}")")
        articles = cur.fetchall()
        return render_template('index.html', articles=articles, page=page)
    except:
        return render_template('index.html', articles=[], page=1)

if __name__ == '__main__':
    app.run('0.0.0.0') # , ssl_context=('cert.crt', 'key.key'))
