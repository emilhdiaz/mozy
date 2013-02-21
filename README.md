~~~~~~~~~~~~~~
Mozy Framework
~~~~~~~~~~~~~~

Mozy is a PHP framework currently being developed to support Model-View-ViewModel (MVVC) architectures for web and mobile applications. 

It differs from existing PHP frameworks in several ways: 

No more UI Templating
~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Traditional PHP applications and frameworks include view templates along side their domains and controllers. These templates are 
processed on the server side and the resulting HTML/CSS/JavaScript is sent back to the browser for rendering. There are several 
disadvantages to this approach though.

First, let's review a little bit of history about HTML itself. HTML is a Domain Specific Language (DSL) adopted by web browsers 
as a means to define a web document's structure. In the early days web pages were simple static markup with very little to no 
interactive features other than forms and hyperlinks. As need for dynamic content grew, templating languages were developed to 
generate dynamic markup, alas PHP. With support still lacking in web browsers for a serious UI runtime environment (remember 
the infant days of JavaScript), developers turned even more attention to templating solutions. Even so that languages not 
originally intended for the web like Java and .NET developed templating extensions (jsp & asp) to allow them to embedded code 
into HTML. This approach has prevailed over the years mainly because templating was the only practical approach to building 
content rich web pages in browsers. In essensence, HTML serves only as an intermediate static representation of a view and any 
futher dynamic nature had to be implemented in JavaScript on the client side. This leaves us with view logic scattered between 
server side templates, HTML, and client side JavaScript. 

Fast forward to the present day and we now have powerful JavaScript engines such as Google's V8. With tremendous improvements in 
performance and support for advanced DOM manipulation, today's browsers provides a UI runtime almost as rich as what is available 
for desktop and native mobile applications. Numerous JavaScript frameworks provide widget libraries that can bind those widgets 
to your domain model allowing them to maintain synchronization with the domain. 

Why then are we still generating HTML on the server side for web apps? Would it not be easier, cleaner, and more effective to develop 
your models and business logic completely independent of the view? Consider what happens when you want to develop a mobile application 
to compliment your site. If you're using templates, typically you would create a new version of those templates for the mobile browser. 
What if you wanted to provide a richer user experience in the form of a native app? At this point you run into complications because 
your original web application is tighltly coupled to the use of templates, so much so that your application controllers and the flow 
of logic are tightly bound to the needs of the those templates. Had a UI agnostic architecture been considered early on adding new 
presentation layers would have zero impact on the flow control logic of the application. 

Mozy is built precisely around this principle. Build a service oriented application core and expose it to any UI you wish. 
Mozy provides the foundation for defining rich domain models and connecting clients via service oriented interfaces.

Domain Focused
~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Mozy helps you focus on the design of your applications's domain and business logic. It employs a powerful Domain 
Definition Language (DDL) allowing you to be more expressive about your domains and their relationships. By using explicit 
types for your domain members, Mozy can validate, filter, and convert user data to guarantee correctness of your domain
and therefore your application. This alleviates the rest of your application from the burden of having to perform such tasks. 
Mozy is backed with an advanced ORM allowing you to persist your domains to relational databases, NoSQL databases and 
even temporarily to memory. 

Mozy's DDL provides a fine grained authorization model for controlling access to your class members and methods. 
It's simply not enough to only have public, protected and private access modifiers. With Mozy you can restrict access 
to individual members or an entire class for use exclusively by another class, interface, namespace, or even a specific 
object instance. Member access can even be more finely controlled through pre-conditions that must be meet by a method's 
caller. This model technique is a closer representation of real world scenarios. For example, typically a person has a 
name and a date of birth. A person may legally change that name if they are an adult of 18 years of age or older or their 
guardian may change it on their behalf if they are under age. With Mozy, this domain logic can be prescribed directly in the 
definition of the method that changes the person's name. There is nothing really revolutionary about imposing such restrictions
on your domains. In fact, typically those restrictions are implemented a layer above in the business logic layer. The real 
difference is that with Mozy those conditions are directly bound to the domain and therefore any part of your application 
that uses those domain objects must abide by those conditions regardless of the entry point. It promotes secure and consistent 
organization of your domain logic. 

Mozy is also able to respond to queries about a domain's DDL allowing clients to autogenerate view components from the domain's definition. 
Imagine working on an application that automatically updates all of it's UIs when a new data member is added to a domain? Nice right? 

Cut the middleman
~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Once again, similar to the HTML case, there are many parts of today's web technology stack which are present only because of legacy. 
One of those cases is the use of a seperate web server. While once a very useful utility for serving up static content, most web applications 
today could benefit from cutting out this middleman. Mozy, similar to node.js, was developed to support an asynchronous I/O model. Mozy can 
operate as a multi-process and/or multi-threaded environment to run native web servers, application servers and even expose service endpoints 
supporting a multitude of protocols such as REST, SOAP and AMQP.  

If you're interested in contributing to this project please email me at emil.h.diaz@gmail.com