

## Running requirements

Background worker needs to be started, to handle remote data fetch

php artisan queue:listen --timeout=0

Data fetching can be done by artisan command, or by api (processing takes a few minutes).

php artisan api:fetch

or

{{ url }}/api/update

Information about the data status, returns last update timestamp: 
{{ url }}/api/info


The test implementation is using mySql database, for performance improvement postgre db could be used, mass upsert is not working properly on mySql.


Found problems:
- some chinese file are having encoding promblems, the exemption was handled, result could be checked on the system log file.
    example : Temple/�ɶ���������ƾ����ĸ���Ʊ������������ģ��.docx  



