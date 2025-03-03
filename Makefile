build-assets:
	cd i/ts \
	&& tsc *.ts \
	&& cd ../webpack \
 	&& npm run build -d
