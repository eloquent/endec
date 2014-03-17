var search_data = {
    'index': {
        'searchIndex': ["eloquent","eloquent\\endec","eloquent\\endec\\base64","eloquent\\endec\\encoding","eloquent\\endec\\encoding\\exception","eloquent\\endec\\hex","eloquent\\endec\\transform","eloquent\\endec\\transform\\exception","eloquent\\endec\\base64\\base64decodetransform","eloquent\\endec\\base64\\base64encodetransform","eloquent\\endec\\encoding\\codecinterface","eloquent\\endec\\encoding\\decoderinterface","eloquent\\endec\\encoding\\encoderinterface","eloquent\\endec\\encoding\\exception\\invalidencodeddataexception","eloquent\\endec\\hex\\hexdecodetransform","eloquent\\endec\\hex\\hexencodetransform","eloquent\\endec\\transform\\abstractdatatransform","eloquent\\endec\\transform\\datatransforminterface","eloquent\\endec\\transform\\exception\\transformexceptioninterface","eloquent\\endec\\transform\\transformstream","eloquent\\endec\\transform\\transformstreaminterface","eloquent\\endec\\base64\\base64decodetransform::instance","eloquent\\endec\\base64\\base64decodetransform::transform","eloquent\\endec\\base64\\base64encodetransform::instance","eloquent\\endec\\base64\\base64encodetransform::transform","eloquent\\endec\\encoding\\decoderinterface::decode","eloquent\\endec\\encoding\\encoderinterface::encode","eloquent\\endec\\encoding\\exception\\invalidencodeddataexception::__construct","eloquent\\endec\\encoding\\exception\\invalidencodeddataexception::encoding","eloquent\\endec\\encoding\\exception\\invalidencodeddataexception::data","eloquent\\endec\\hex\\hexdecodetransform::instance","eloquent\\endec\\hex\\hexdecodetransform::transform","eloquent\\endec\\hex\\hexencodetransform::instance","eloquent\\endec\\hex\\hexencodetransform::transform","eloquent\\endec\\transform\\datatransforminterface::transform","eloquent\\endec\\transform\\transformstream::__construct","eloquent\\endec\\transform\\transformstream::transform","eloquent\\endec\\transform\\transformstream::buffersize","eloquent\\endec\\transform\\transformstream::iswritable","eloquent\\endec\\transform\\transformstream::isreadable","eloquent\\endec\\transform\\transformstream::write","eloquent\\endec\\transform\\transformstream::end","eloquent\\endec\\transform\\transformstream::close","eloquent\\endec\\transform\\transformstream::pause","eloquent\\endec\\transform\\transformstream::resume","eloquent\\endec\\transform\\transformstream::pipe","eloquent\\endec\\transform\\transformstreaminterface::transform"],
        'info': [["Eloquent","","Eloquent.html","","",3],["Eloquent\\Endec","","Eloquent\/Endec.html","","",3],["Eloquent\\Endec\\Base64","","Eloquent\/Endec\/Base64.html","","",3],["Eloquent\\Endec\\Encoding","","Eloquent\/Endec\/Encoding.html","","",3],["Eloquent\\Endec\\Encoding\\Exception","","Eloquent\/Endec\/Encoding\/Exception.html","","",3],["Eloquent\\Endec\\Hex","","Eloquent\/Endec\/Hex.html","","",3],["Eloquent\\Endec\\Transform","","Eloquent\/Endec\/Transform.html","","",3],["Eloquent\\Endec\\Transform\\Exception","","Eloquent\/Endec\/Transform\/Exception.html","","",3],["Base64DecodeTransform","Eloquent\\Endec\\Base64","Eloquent\/Endec\/Base64\/Base64DecodeTransform.html"," < AbstractDataTransform","Decodes data using base64 encoding.",1],["Base64EncodeTransform","Eloquent\\Endec\\Base64","Eloquent\/Endec\/Base64\/Base64EncodeTransform.html"," < AbstractDataTransform","Encodes data using base64 encoding.",1],["CodecInterface","Eloquent\\Endec\\Encoding","Eloquent\/Endec\/Encoding\/CodecInterface.html","","The interface implemented by codecs.",1],["DecoderInterface","Eloquent\\Endec\\Encoding","Eloquent\/Endec\/Encoding\/DecoderInterface.html","","The interface implemented by decoders.",1],["EncoderInterface","Eloquent\\Endec\\Encoding","Eloquent\/Endec\/Encoding\/EncoderInterface.html","","The interface implemented by encoders.",1],["InvalidEncodedDataException","Eloquent\\Endec\\Encoding\\Exception","Eloquent\/Endec\/Encoding\/Exception\/InvalidEncodedDataException.html"," < Exception","The supplied data is not correctly encoded.",1],["HexDecodeTransform","Eloquent\\Endec\\Hex","Eloquent\/Endec\/Hex\/HexDecodeTransform.html"," < AbstractDataTransform","Decodes data using hexadecimal encoding.",1],["HexEncodeTransform","Eloquent\\Endec\\Hex","Eloquent\/Endec\/Hex\/HexEncodeTransform.html","","Encodes data using hexadecimal encoding.",1],["AbstractDataTransform","Eloquent\\Endec\\Transform","Eloquent\/Endec\/Transform\/AbstractDataTransform.html","","An abstract base class for implementing data transforms.",1],["DataTransformInterface","Eloquent\\Endec\\Transform","Eloquent\/Endec\/Transform\/DataTransformInterface.html","","The interface implemented by data transforms.",1],["TransformExceptionInterface","Eloquent\\Endec\\Transform\\Exception","Eloquent\/Endec\/Transform\/Exception\/TransformExceptionInterface.html","","The interface used to identify transform exceptions.",1],["TransformStream","Eloquent\\Endec\\Transform","Eloquent\/Endec\/Transform\/TransformStream.html","","A stream wrapper for data transforms.",1],["TransformStreamInterface","Eloquent\\Endec\\Transform","Eloquent\/Endec\/Transform\/TransformStreamInterface.html","","The interface implemented by transform stream wrappers.",1],["Base64DecodeTransform::instance","Eloquent\\Endec\\Base64\\Base64DecodeTransform","Eloquent\/Endec\/Base64\/Base64DecodeTransform.html#method_instance","()","Get the static instance of this transform.",2],["Base64DecodeTransform::transform","Eloquent\\Endec\\Base64\\Base64DecodeTransform","Eloquent\/Endec\/Base64\/Base64DecodeTransform.html#method_transform","(string $data, boolean $isEnd = false)","Transform the supplied data.",2],["Base64EncodeTransform::instance","Eloquent\\Endec\\Base64\\Base64EncodeTransform","Eloquent\/Endec\/Base64\/Base64EncodeTransform.html#method_instance","()","Get the static instance of this transform.",2],["Base64EncodeTransform::transform","Eloquent\\Endec\\Base64\\Base64EncodeTransform","Eloquent\/Endec\/Base64\/Base64EncodeTransform.html#method_transform","(string $data, boolean $isEnd = false)","Transform the supplied data.",2],["DecoderInterface::decode","Eloquent\\Endec\\Encoding\\DecoderInterface","Eloquent\/Endec\/Encoding\/DecoderInterface.html#method_decode","(string $data)","Decode the supplied data.",2],["EncoderInterface::encode","Eloquent\\Endec\\Encoding\\EncoderInterface","Eloquent\/Endec\/Encoding\/EncoderInterface.html#method_encode","(string $data)","Encode the supplied data.",2],["InvalidEncodedDataException::__construct","Eloquent\\Endec\\Encoding\\Exception\\InvalidEncodedDataException","Eloquent\/Endec\/Encoding\/Exception\/InvalidEncodedDataException.html#method___construct","(string $encoding, string|null $data = null, <a href=\"http:\/\/php.net\/Exception\"><abbr title=\"Exception\">Exception<\/abbr><\/a> $cause = null)","Construct a new invalid encoded data exception.",2],["InvalidEncodedDataException::encoding","Eloquent\\Endec\\Encoding\\Exception\\InvalidEncodedDataException","Eloquent\/Endec\/Encoding\/Exception\/InvalidEncodedDataException.html#method_encoding","()","Get the name of the expected encoding.",2],["InvalidEncodedDataException::data","Eloquent\\Endec\\Encoding\\Exception\\InvalidEncodedDataException","Eloquent\/Endec\/Encoding\/Exception\/InvalidEncodedDataException.html#method_data","()","Get the invalid data.",2],["HexDecodeTransform::instance","Eloquent\\Endec\\Hex\\HexDecodeTransform","Eloquent\/Endec\/Hex\/HexDecodeTransform.html#method_instance","()","Get the static instance of this transform.",2],["HexDecodeTransform::transform","Eloquent\\Endec\\Hex\\HexDecodeTransform","Eloquent\/Endec\/Hex\/HexDecodeTransform.html#method_transform","(string $data, boolean $isEnd = false)","Transform the supplied data.",2],["HexEncodeTransform::instance","Eloquent\\Endec\\Hex\\HexEncodeTransform","Eloquent\/Endec\/Hex\/HexEncodeTransform.html#method_instance","()","Get the static instance of this transform.",2],["HexEncodeTransform::transform","Eloquent\\Endec\\Hex\\HexEncodeTransform","Eloquent\/Endec\/Hex\/HexEncodeTransform.html#method_transform","(string $data, boolean $isEnd = false)","Transform the supplied data.",2],["DataTransformInterface::transform","Eloquent\\Endec\\Transform\\DataTransformInterface","Eloquent\/Endec\/Transform\/DataTransformInterface.html#method_transform","(string $data, boolean $isEnd = false)","Transform the supplied data.",2],["TransformStream::__construct","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method___construct","(<a href=\"Eloquent\/Endec\/Transform\/DataTransformInterface.html\"><abbr title=\"Eloquent\\Endec\\Transform\\DataTransformInterface\">DataTransformInterface<\/abbr><\/a> $transform, integer|null $bufferSize = null)","Construct a new data transform.",2],["TransformStream::transform","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_transform","()","Get the transform.",2],["TransformStream::bufferSize","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_bufferSize","()","Get the buffer size.",2],["TransformStream::isWritable","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_isWritable","()","Returns true if this transform is writable.",2],["TransformStream::isReadable","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_isReadable","()","Returns true if this transform is readable.",2],["TransformStream::write","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_write","(string $data)","Write some data to this transform.",2],["TransformStream::end","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_end","(string|null $data = null)","Transform and finalize any remaining buffered data.",2],["TransformStream::close","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_close","()","Close this transform.",2],["TransformStream::pause","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_pause","()","Pause this transform.",2],["TransformStream::resume","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_resume","()","Resume this transform.",2],["TransformStream::pipe","Eloquent\\Endec\\Transform\\TransformStream","Eloquent\/Endec\/Transform\/TransformStream.html#method_pipe","(<abbr title=\"React\\Stream\\WritableStreamInterface\">WritableStreamInterface<\/abbr> $destination, array $options = array())","Pipe the output of this transform to another stream.",2],["TransformStreamInterface::transform","Eloquent\\Endec\\Transform\\TransformStreamInterface","Eloquent\/Endec\/Transform\/TransformStreamInterface.html#method_transform","()","Get the transform.",2]]
    }
}
search_data['index']['longSearchIndex'] = search_data['index']['searchIndex']