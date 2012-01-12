# Acl Extras

Acl Extras provides a console app that helps you manage DbAcl records more easily.  Its main feature and purpose is to make generating Aco nodes for all your controllers and actions easier.  It also includes some helper methods for verifying and recovering corrupted trees.

# Croogo Integration

At this point, this plugin is in pre-alpha status, and only compatible with my
custom Croogo 1.4.x (pluggable-auth branch).  It provides better compatibility
with standard cakephp ACL with respect to ACO handling for plugins.

## Installation

Clone the repo or download a tarball and install it into `Plugin/AclExtras` and activate it via Extensions -> Plugins menu.

## Usage

You can find a list of commands by running `Console/cake AclExtras.AclExtras -h` from your command line.

## Issues 

If you find an issue in the code or want to suggest something, please use the tickets at http://github.com/rchavik/acl_extras/issues

## License

Acl Extras is licensed under the MIT license.