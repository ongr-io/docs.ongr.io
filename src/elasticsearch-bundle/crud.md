# CRUD actions

> To proceed with steps bellow it is necessary to read [mapping](mapping.md) topic and have defined documents in the bundle.

For all steps below we assume that there is a `Content` index in your project.

```php
// src/Document/Content.php

namespace App\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\Index(alias="content")
 */
class Content
{
    /**
     * @var string
     *
     * @ES\Id()
     */
    public $id;

    /**
     * @ES\Property(type="keyword")
     */
    public $title;
}
```

## How to get Index service?

Elasticsearch bundle provides `IndexService` which provides all CRUD and search operations for a defined elasticsearch index.

```php
use App\Document\Content;

\\...

$index = $this->get(Content::class);

```

Here you go. Index will have loaded `Content` document structure to work with and a link to that index which
 represents this object.

## Create a document

```php

$content = new Content();
$content->id = 5; // Optional, if not set, elasticsearch will set a random.
$content->title = 'Acme title';
$index->persist($content);
$index->commit();

```

## Update a document

```php

$content = $manager->find('AppBundle:Content', 5);
$content->title = 'changed Acme title';
$index->persist($content);
$index->commit();

```

## Partial update

There is a quicker way to update a document field without creating object or fetching a whole document from elasticsearch. For this action we will use [partial update](https://www.elastic.co/guide/en/elasticsearch/guide/current/partial-updates.html) from elasticsearch.

To update a field you need to know the document `ID` and fields to update. Here's an example:

```php

$index->update(1, ['title' => 'new title']);

// or

$index->update($content->getId(), ['title' => 'new title']);

```

You can also update fields with script operation, lets say, you want to do some math:

```php

$index->update(1, [], 'ctx._source.stock+=1');

```
> Important: when using script update fields cannot be updated, leave empty array, otherwise you will get 400 exception.

`ctx._source` comes from painless scripting. By default it is disabled, don't forget to enable scripting

> More information about scripting in [the elasticsearch docs](https://www.elastic.co/guide/en/elasticsearch/painless/current/painless-lang-spec.html) 


In addition you also can get other document fields with the response of update, lets say we also want a content field and a new title, so just add them separated by a comma:


```php

$response = $index->update(1, ['title' => 'new title'], null, ['fields' => 'title,content']);

```


## Delete a document

Document removal can be performed similarly to create or update action:

```php
$index->remove($content);
```

Alternatively you can remove document by ID (requires to have repository service):

```php

$content = $repo->remove(5);

```
