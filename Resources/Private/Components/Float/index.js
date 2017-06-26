import { MF } from '../Form';

MF('.form-input-field,.form-textarea-field').input({
	className: {
		hasValue: 'form-has-value'
	}
});

MF('.form-select-field').dropdown({
	className: {
		hasValue: 'form-has-value',
		dropdown: 'form-select-dropdown',
		list: 'form-select-dropdown-menu',
		right: 'form-select-dropdown-right',
		button: 'form-select-btn',
		open: 'form-select-open',
		select: 'form-select'
	}
});

MF('.form-file').file({
	selector: {
		input: '.form-file-field',
		files: '.form-file-files'
	}
});

MF('.form-collapse-input').collapse({
	className: {
		visible: 'form-collapse-input-visible',
		hidden: 'form-collapse-input-hidden'
	}
});
