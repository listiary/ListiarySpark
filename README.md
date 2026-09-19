<p align="center">
<img width="520" height="120" alt="listiary-wiki-logo-cropped" src="https://github.com/user-attachments/assets/02d3faff-e4cf-49f8-a771-c7b8fbb483e0" />
</p>

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL_v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)

Spark Lib is an administrative layer for managing the Listiary database. Spark CLI is a standalone PHP CLI application that uses Spark Lib to perform administrative tasks from the console.

Spark is powerful and unforgiving. Be careful which script you invoke and read the documentation before using it.

## Basic Architecture

The Listiary Spark framework, or Spark for short, consists of a core library and several tools that use it:

* **Spark Lib**: The library containing Spark's core functionality for managing Listiary databases.
* **Spark CLI**: A command-line interface for administering Listiary instances from the console.
* **Spark Web**: A planned HTTP API interface for Spark, providing the same functionality as Spark CLI remotely.
* **Listiary Admin Module**: The administrative interface built into Listiary. It provides much of the same functionality as Spark, but is limited to the current wiki instance.
