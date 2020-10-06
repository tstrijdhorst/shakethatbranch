# Shake That Branch
### Hierarchical branch management made easy

_This project is currently in beta. Feedback is appreciated. Do not trust it with your life._

Splitting up featurebranches into multiple PRs for easy reviewing is really nice. Except if you get a comment
on the first PR then you need to merge and push those changes into it's children (and their children). This can become really cumbersome quite quickly.

This tool fixes that by letting you define childbranches for a git branch and giving you tools to automatically merge (and push) the changes of a parent.

## Quick Start:
Use the `stb` command to see all available commands and their flags.

### Installation
* install php > 7.4

#### Repository

* clone this repository
* run `composer install`
* add an alias to your shell like `alias stb="php /home/tim/repositories/shakethatbranch/stb.php"`
* use `stb` command wherever you want

#### PHAR

* download phar file from releases on github
* make phar file executable `chmod u+x stb.phar`
* place phar file in your $PATH

### Example usage

Create a parent branch, add a child branch and give that branch a child as well.  
So we have a tree hierarchy: Parent -> Child1 -> Child11

```
git init
stb init
git checkout -b parent
echo "test" > testfile        #create a new file to test with
git add testfile
git commit -m "commit1"    
git checkout -b child1        #create childbranch 1
git checkout -b child11       #create childbranch 1-1
git checkout parent
stb add child1                #add child1 as child of parent
git checkout child1
git add child11               #add child11 as child of child1
git checkout parent
echo "test12" > testfile      #change the file to make a new commit
git add testfile
git commit -m "commit2"        
stb merge-into-children -r    #-r means recursive, so merge into children of children
git log                       #you will now see that (HEAD -> parent, child11, child1)
```


```
stb push -r                   #if you have an origin set this will push parent, child1 and child11
```