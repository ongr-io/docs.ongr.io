# -- autopull repos

import string

lines = [line.strip() for line in open('%s/components/.repos' % (os.path.dirname(os.path.realpath(__file__))))]

for line in lines:
    if ( line[0]!="#" ) :
        repository_url, doc_directory = string.split(line, ' ')
        if doc_directory == '~':
            doc_directory = 'Resources/doc'

        repository_name = repository_url.split("/")[-1]
        os.system("mkdir _components; cd _components; mkdir %s; cd %s; git init; git remote add -f origin %s; git config core.sparsecheckout true; echo '%s' > .git/info/sparse-checkout; echo 'Resources/API' >> .git/info/sparse-checkout; git reset --hard; git pull origin master; cd ../../components; rm -rf %s; ln -s ../_components/%s/%s %s; cd .." % (repository_name, repository_name, repository_url, doc_directory, repository_name, repository_name, doc_directory, repository_name))

# fix links to githubs' .rst files.
os.system("grep -rl '.rst' components/ | grep -v '/.git/' | xargs perl -pe 's/\`(.*?)<(.*?)\.rst>\`_/:doc:`$1<$2>`/g' -i")
