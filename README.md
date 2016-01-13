# ONGR docs builder

This is [http://docs.ongr.io](http://docs.ongr.io) docs site code. It can gather docs from multiple repositories, 
convert to HTML and index them in to the Elasticsearch. 

## Requirements

    * Elasticsearch >=2.0
    * PHP >=5.5

## Install

This website is based on Symfony framework. To get it running folow the steps below:
 
### Step 1

Clone the repository:

```bash

git clone https://github.com/ongr-io/docs.ongr.io.git

```

### Step 2

Install vendors via [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx):

```bash

composer install --prefer-dist

```

### Step 3

Create elasticsearch index. Before creating index you might want to change index name and elasticsearch 
connection settings. To do that take a look at the Symfony configuration file (`app/config/config.yml`) 
under `ongr_elasticsearch` node.

Run:

```bash

bin/console ongr:es:index:create

```

### Step 4 (optional)

To run site quickly at the local env you can do it via PHP built-in server, run:
 
```bash

bin/console server:run

```

## Usage


### Github token

For the Github synchronization you have to get access token, how to get it can be found 
in the [Official Github docs](https://help.github.com/articles/creating-an-access-token-for-command-line-use/). 
Once you have it add it to the parameters.yml:

```yaml

#app/config/parameters.yml

parameters:
    #...
    github_token: YourNewTokenHere

```

> By default the token is set to `xxx` in the `parameters.yml.dist`.


### Repositories config

For repositories synchronization you have to add a list of repositories you want to sync in `repos` parameter. 
E.g. our list is defined in global `services.yml`:
 
 
```yaml

#app/config/services.yml

parameters:
    #...
    repos:
        - {org: ongr-io, repo: ElasticsearchBundle}
        - {org: ongr-io, repo: FilterManagerBundle}
        - {org: ongr-io, repo: ElasticsearchDSL}
        - {org: ongr-io, repo: RouterBundle}
        - {org: ongr-io, repo: ApiBundle}

```

### Sync command

There is a CLI command which runs the sync through all configured repositories and add's it to the index.

Run:

```bash

bin/console ongr:md:sync

```