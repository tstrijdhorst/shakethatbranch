# Shake That Branch
### Hierarchical branch management made easy

Splitting up featurebranches into multiple PRs for easy reviewing is really nice. Except if you get a comment
on the first PR then you need to merge and push those changes into it's children (and their children). This can become really cumbersome quite quickly.

This tool fixes that by letting you define childbranches for a git branch and giving you tools to automatically merge (and push) the changes of a parent.

## Quick Start:
Use the `stb` command to see all available commands and their flags.

### Example usage

Create a parent branch, add a child branch and give that branch a child as well.  
So we have a tree hierarchy: Parent -> Child1 -> Child11

```
git init
stb init
git checkout -b parent
git checkout -b child1
git checkout -b child11
git checkout parent
stb add child1
git checkout child1
git add child11
git checkout parent
echo "test" > testfile
git commit -ma "test commit"
stb merge-into-children -r     #-r means recursive, so merge into children of children
git log                        #You will now see that the HEAD of <parent, child1, child11> is all set to the last commit
```