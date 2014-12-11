# -- autopull repos



lines = [line.strip() for line in open('%s/sources/.repos' % (os.path.dirname(os.path.realpath(__file__))))]

for line in lines:
    if ( line[0]!="#" ) :
        os.system("cd sources; mkdir %s; cd %s; git init; git remote add -f origin %s; git config core.sparsecheckout true; echo 'Resources/doc' > .git/info/sparse-checkout; echo 'Resources/API' >> .git/info/sparse-checkout; git pull origin master;" % (line.split("/")[-1], line.split("/")[-1], line))

# fix links to githubs' .rst files.
os.system("grep -rl '.rst' sources/ | grep -v '/.git/' | xargs perl -pe 's/(\.\. _.*: |\`.*<)(.*)\.rst(>\`_|)/$1:doc:`$2`/g' -i")
os.system("grep -rl ':doc:' sources/ | grep -v '/.git/' | xargs perl -pe 's/(.*)(`.*):doc:/$1:doc:/g' -i")
os.system("grep -rl ':doc:' sources/ | grep -v '/.git/' | xargs perl -pe 's/(\.\. _.*:):doc:(.*)/$1$2.html/g' -i")
