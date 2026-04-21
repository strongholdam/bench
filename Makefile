CONSOLE = php bin/console

.PHONY: cpu io

cpu:
	$(CONSOLE) benchmark:cpu

io:
	$(CONSOLE) benchmark:io
