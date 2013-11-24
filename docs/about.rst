Zunzuncito
===========

micro-framework for creating REST API's.

Design Goals
------------
* Keep it simple and small, avoiding extra complexity at all cost. `KISS <http://en.wikipedia.org/wiki/KISS_principle>`_
* Creation of routes on the fly or by defining regular expressions.
* Support API versions out of the box without altering routes.
* Via decorator or in a defined route, accept only certain `HTTP methods <http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html>`_.
* Follow the single responsibility `principle <http://en.wikipedia.org/wiki/Single_responsibility_principle>`_.
* Be compatible with any WSGI server, example: `uWSGI <http://uwsgi-docs.readthedocs.org/en/latest/>`_, `Gunicorn <http://gunicorn.org/>`_, `Twisted <http://twistedmatrix.com/>`_, etc.
* Structured Logging using `JSON <http://en.wikipedia.org/wiki/JSON>`_.
* No template rendering.
* Tracing Request-ID "rid" per request.
* Compatibility with Google App Engine. `demo <http://api.zunzun.io>`_
* `Multi-tenant <http://en.wikipedia.org/wiki/Multitenancy>`_ Support.

What & Why ZunZuncito
---------------------

ZunZuncito is a `python <http://python.org/>`_ package that allows to create and maintain `REST <http://en.wikipedia.org/wiki/REST>`_ API's without hassle.

The simplicity for sketching and debugging helps to develop very fast; versioning is inherit by default, which allows to serve and maintain existing applications, while working in new releases without need to create separate instances. All the applications are WSGI `PEP 333 <http://www.python.org/dev/peps/pep-0333/>`_ compliant, allowing to migrate existing code to more robust frameworks, without need to modify the existing code.

The idea of creating ZunZuncito, was the need of a very small and light tool (batteries included), that could help to create and deploy REST API's quickly, without forcing the developers to learn or follow a complex flow but, in contrast, from the very beginning, guide them to properly structure their API, giving special attention to "versioned URI's", having with this a solid base that allows to work in different versions within a single ZunZun instance without interrupting service of any existing API `resources <http://en.wikipedia.org/wiki/Web_resource>`_.
