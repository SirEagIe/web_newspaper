import requests
import xml.etree.ElementTree as ET
import re
import psycopg2
from time import sleep

rss_feeds = [
    'https://dtf.ru/rss/games',
    'https://dtf.ru/rss/cinema',
    'https://dtf.ru/rss/gameindustry',
    'https://dtf.ru/rss/gamedev',
    'https://dtf.ru/rss/life',
    'https://dtf.ru/rss/hard',
    'https://lenta.ru/rss',
    'https://ria.ru/export/rss2/archive/index.xml',
    'https://habr.com/ru/rss/all/all/?fl=ru',
    'https://russian.rt.com/rss',
    'https://www.rt.com/rss/',
    'https://www.rt.com/rss/business/',
    'https://www.rt.com/rss/pop-culture/',
]

conn = psycopg2.connect(dbname='test', user='admin', password='admin', host='pg_db', port='5432')
cursor = conn.cursor()

cursor.execute(
    f"""
    CREATE TABLE IF NOT EXISTS articles (
        id INT GENERATED ALWAYS AS IDENTITY,
        title varchar(1024) NULL,
        description varchar(2048) NULL,
        link varchar(2048) NULL,
        pubDate timestamp NULL,
        image varchar(2048) NULL,
        rss_feed varchar(1024) NULL
    );
    """
)

def normalize_description(description: str):
    description = description.replace('    ', '')
    description = description.replace('\n', '')
    description = description.replace("'", "")
    description = re.sub(r'<.*?>', '', description)
    return description.strip()

while 1:
    print('==start==')
    for rss_feed in rss_feeds:
        response = requests.get(rss_feed)
        print(rss_feed)
        with open('/tmp/temp.xml', 'w', encoding="utf-8") as file:
            file.write(response.text)
        xml_tree = ET.parse('/tmp/temp.xml')
        for item in xml_tree.findall('channel/item'):
            title = item.find('title').text.replace("'", "")
            description = item.find('description')
            if description is None:
                description = normalize_description(title)
            else:
                description = normalize_description(description.text)
            link = item.find('link').text
            pubDate = item.find('pubDate').text
            enclosure = item.find('enclosure')
            if enclosure is None:
                enclosure = 'https://www.rbs.ca/wp-content/themes/rbs/images/news-placeholder.png'
            else:
                enclosure = enclosure.attrib['url']
            cursor.execute(
                f"""
                SELECT
                    *
                FROM
                    articles
                WHERE
                    title='{title}' OR link='{link}'
                """
            )
            if not cursor.fetchall():
                print(title)
                cursor.execute(
                    f"""
                    INSERT INTO
                        articles (title, description, link, pubDate, image, rss_feed)
                    VALUES
                        ('{title}', '{description}', '{link}', '{pubDate}', '{enclosure}', '{rss_feed}')
                    """
                )
    cursor.execute(
        f"""
        DELETE FROM articles
        WHERE pubDate < now() - interval '30 days'
        """
    )
    conn.commit()
    sleep(300)
