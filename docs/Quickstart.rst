Quick Start
===========

This is the directory structure::

   /home/
     `--zunzun/
        |--app.py
        `--my_api
          |--__init__.py
          `--default
            |--__init__.py
            |--v0
            |  |--__init__.py
            |  |--zun_default
            |  |  |--__init__.py
            |  |  `--zun_default.py
            |  `--zun_hasher
            |    |--__init__.py
            |    `--zun_hasher.py
            `--v1
               |--__init__.py
               |--zun_default
               | |--__init__.py
               | `--zun_default.py
               `--zun_hasher
                 |--__init__.py
                 `--zun_hasher.py

Inside directory /home/zunzun there is a file called **app.py** and a directory **my_api**.

For a very basic API, contents of file **app.py** can be:

.. code-block:: python
   :emphasize-lines: 3
   :linenos:

   import zunzuncito

   root = 'my_api'

   versions = ['v0', 'v1']

   hosts = {'*': 'default'}

   routes = {'default':[
       ('/.*', 'default'),
       ('/(md5|sha1|sha256|sha512)(/.*)?', 'hasher', 'GET, POST')
   ]}

   app = zunzuncito.ZunZun(root, versions, routes)


The contents of the **my_api** contain python modules (API Resources) for
example the content of module zun_default/zun_default.py is:

.. code-block:: python
   :linenos:

   import json
   import logging
   from zunzuncito import http_status_codes
   from zunzuncito.tools import MethodException, HTTPException, allow_methods


   class APIResource(object):

   def __init__(self, api):
       self.api = api
       self.status = 200
       self.headers = api.headers.copy()
       self.log = logging.getLogger()
       self.log.setLevel('INFO')
       self.log = logging.LoggerAdapter(
           logging.getLogger(), {
               'rid': api.request_id,
               'indent': 4
           })
       self.log.info(dict((x, y) for x, y in (
           ('API', api.version),
           ('URI', api.URI),
           ('method', api.method)
       )))

   @allow_methods('get')
   def dispatch(self, environ, start_response):
       headers = self.api.headers
       start_response(
           getattr(http_status_codes, 'HTTP_%d' %
                   self.status), list(headers.items()))
       data = {}
       data['about'] = ("Hi %s, I am zunzuncito a micro-framework for creating"
                        " REST API's, you can read more about me in: "
                        "www.zunzun.io") % environ.get('REMOTE_ADDR', 0)
       data['request-id'] = self.api.request_id
       data['URI'] = self.api.URI
       data['method'] = self.api.method

       return json.dumps(data, sort_keys=True, indent=4)
