bunkerer
========

What
----
bunkerer is a small PHP app that provides an interface for retrieving hosts file entries required to block access to several [BOINC](https://boinc.berkeley.edu/) projects.

Why
---
This is needed in order to prevent workunits to be uploaded to project servers, which often is done before or during challenges. Using this technique, tasks can be crunched even before the challenge started. That's because for most challenges, it's important when the workunits were reported, not when they were actually finished.
