# -*- coding: utf-8 -*-

execfile("conf-travis.py")

# Check that all links are linking to existing documents.
nitpicky = True

# Getting theme.
execfile("theme.py")

# Pulling all repository components.
execfile("pull.py")
